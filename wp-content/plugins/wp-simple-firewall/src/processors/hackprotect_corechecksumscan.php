<?php

if ( !class_exists( 'ICWP_WPSF_Processor_HackProtect_CoreChecksumScan', false ) ):

	require_once( dirname(__FILE__).ICWP_DS.'base.php' );

	class ICWP_WPSF_Processor_HackProtect_CoreChecksumScan extends ICWP_WPSF_Processor_Base {

		/**
		 */
		public function run() {
			$this->setupChecksumCron();
			if ( $this->loadDataProcessor()->FetchGet( 'force_checksumscan' ) == 1 && $this->loadWpUsersProcessor()->isUserAdmin() ) {
				$this->cron_dailyChecksumScan();
			}
		}

		protected function setupChecksumCron() {
			$oWpCron = $this->loadWpCronProcessor();
			$oWpCron->createCronJob(
				$this->getCronName(),
				array( $this, 'cron_dailyChecksumScan' ),
				'daily'
			);
			add_action( $this->getFeatureOptions()->doPluginPrefix( 'delete_plugin' ), array( $this, 'deleteCron' )  );
		}

		/**
		 */
		public function deleteCron() {
			$this->loadWpCronProcessor()->deleteCronJob( $this->getCronName() );
		}

		public function cron_dailyChecksumScan() {

			$aChecksumData = $this->loadWpFunctionsProcessor()->getCoreChecksums();

			if ( !empty( $aChecksumData ) && is_array( $aChecksumData ) ) {

				$aDiscoveredFiles = array(
					'checksum_mismatch' => array(),
					'missing' => array(),
				);

				$aAutoFixIndexFiles = $this->getFeatureOptions()->getDefinition( 'corechecksum_autofix_index_files' );
				if ( empty( $aAutoFixIndexFiles ) ) {
					$aAutoFixIndexFiles = array();
				}

				$sFullExclusionsPattern = '#('.implode('|', $this->getFullExclusions() ).')#i';
				$sMissingOnlyExclusionsPattern = '#('.implode('|', $this->getMissingOnlyExclusions() ).')#i';
				$bOptionRepair = $this->getIsOption( 'attempt_auto_file_repair', 'Y' );

				$oFS = $this->loadFileSystemProcessor();
				foreach ( $aChecksumData as $sFilePath => $sChecksum ) {
					if ( preg_match( $sFullExclusionsPattern, $sFilePath ) ) {
						continue;
					}

					$bRepairThis = false;
					$sFullPath = ABSPATH . $sFilePath;

					if ( $oFS->isFile( $sFullPath ) ) {
						if ( $sChecksum != md5_file( $sFullPath ) ) {

							if ( in_array( $sFilePath, $aAutoFixIndexFiles ) ) {
								$bRepairThis = true;
							}
							else {
								$aDiscoveredFiles[ 'checksum_mismatch' ][] = $sFilePath;
								$bRepairThis = $bOptionRepair;
							}
						}
					}
					else if ( !preg_match( $sMissingOnlyExclusionsPattern, $sFilePath ) ) {
						// If the file is missing and it's not in the missing-only exclusions
						$aDiscoveredFiles[ 'missing' ][] = $sFilePath;
						$bRepairThis = $bOptionRepair;
					}

					if ( $bRepairThis ) {
						$this->replaceFileContentsWithOfficial( $sFilePath );
					}
				}

				if ( !empty( $aDiscoveredFiles[ 'checksum_mismatch' ] ) || !empty( $aDiscoveredFiles[ 'missing' ] ) ) {
					$this->sendChecksumErrorNotification( $aDiscoveredFiles );
				}
			}
		}

		/**
		 * @return array
		 */
		protected function getFullExclusions() {
			$aExclusions = $this->getFeatureOptions()->getDefinition( 'corechecksum_exclusions' );
			if ( empty( $aExclusions ) || !is_array( $aExclusions ) ) {
				$aExclusions = array();
			}
			foreach ( $aExclusions as $nKey => $sExclusion ) {
				$aExclusions[ $nKey ] = preg_quote( $sExclusion, '#' );
			}
			return $aExclusions;
		}

		/**
		 * @return array
		 */
		protected function getMissingOnlyExclusions() {
			$aExclusions = $this->getFeatureOptions()->getDefinition( 'corechecksum_exclusions_missing_only' );
			if ( empty( $aExclusions ) || !is_array( $aExclusions ) ) {
				$aExclusions = array();
			}
			foreach ( $aExclusions as $nKey => $sExclusion ) {
				$aExclusions[ $nKey ] = preg_quote( $sExclusion, '#' );
			}
			return $aExclusions;
		}

		/**
		 * @param $sPath
		 * @return false|string
		 */
		protected function downloadSingleWordPressCoreFile( $sPath ) {
			$sBaseSvnUrl = $this->getFeatureOptions()->getDefinition( 'url_wordress_core_svn' ).'tags/'.$this->loadWpFunctionsProcessor()->getWordpressVersion().'/';
			$sFileUrl = path_join( $sBaseSvnUrl, $sPath );
			return $this->loadFileSystemProcessor()->getUrlContent( $sFileUrl );
		}

		/**
		 * @param string $sPath
		 * @return bool|null
		 */
		protected function replaceFileContentsWithOfficial( $sPath ) {
			$sOfficialContent = $this->downloadSingleWordPressCoreFile( $sPath );
			if ( !empty( $sOfficialContent ) ) {
				return $this->loadFileSystemProcessor()->putFileContent( path_join( ABSPATH, $sPath ), $sOfficialContent );
			}
			return false;
		}

		/**
		 * @param array $aFiles
		 * @return bool
		 */
		protected function sendChecksumErrorNotification( $aFiles ) {
			if ( empty( $aFiles ) && empty( $aFiles['missing'] ) && empty( $aFiles['checksum_mismatch'] ) ) {
				return true;
			}

			$oWp = $this->loadWpFunctionsProcessor();
			$aContent = array(
				sprintf( _wpsf__( '%s has detected files on your site with potential problems.' ), $this->getController()->getHumanName() ),
				_wpsf__( 'This is part of the Hack Protection feature for the WordPress Core File Scanner.' )
				. ' [<a href="http://icwp.io/moreinfochecksum">'._wpsf__('More Info').']</a>',
				sprintf( 'Site - %s', sprintf( '<a href="%s" target="_blank">%s</a>', $oWp->getHomeUrl(), $oWp->getSiteName() ) ),
				'',
				_wpsf__( 'Details for the problem files are below:' ),
			);

			$sBaseSvnUrl = $this->getFeatureOptions()->getDefinition( 'url_wordress_core_svn' ).'tags/'.$this->loadWpFunctionsProcessor()->getWordpressVersion().'/';
			if ( !empty( $aFiles['checksum_mismatch'] ) ) {
				$aContent[] = '';
				$aContent[] = _wpsf__('The MD5 Checksum Hashes for following core files do not match the official WordPress.org Checksum Hashes:');
				foreach( $aFiles['checksum_mismatch'] as $sFile ) {
					$sSource = $sBaseSvnUrl . $sFile;
					$aContent[] = ' - ' . $sFile .sprintf( ' (<a href="%s">', $sSource )._wpsf__( 'WordPress.org source file' ).'</a>)';
				}
			}
			if ( !empty( $aFiles['missing'] ) ) {
				$aContent[] = '';
				$aContent[] = _wpsf__('The following official WordPress core files are missing from your site:');
				foreach( $aFiles['missing'] as $sFile ) {
					$sSource = $sBaseSvnUrl . $sFile;
					$aContent[] = ' - ' . $sFile .sprintf( ' (<a href="%s">', $sSource )._wpsf__( 'WordPress.org source file' ).'</a>)';
				}
			}

			$aContent[] = '';
			if ( $this->getIsOption( 'attempt_auto_file_repair', 'Y' ) ) {
				$aContent[] = _wpsf__( 'We have already attempted to repair these files based on your current settings.' )
				.' '._wpsf__( 'But, you should always check these files to ensure everything is as you expect.' );
			}
			else {
				$aContent[] = _wpsf__( 'You should review these files and replace them with official versions if required.' );
				$aContent[] = _wpsf__( 'Alternatively you can have the plugin attempt to repair/replace these files automatically.' )
					. ' [<a href="http://icwp.io/moreinfochecksum">'._wpsf__('More Info').']</a>';
			}

			$sRecipient = $this->getPluginDefaultRecipientAddress();
			$sEmailSubject = sprintf( _wpsf__( 'Warning - %s' ), _wpsf__( 'Core WordPress Files(s) Discovered That May Have Been Modified.' ) );
			$bSendSuccess = $this->getEmailProcessor()->sendEmailTo( $sRecipient, $sEmailSubject, $aContent );

			if ( $bSendSuccess ) {
				$this->addToAuditEntry( sprintf( _wpsf__( 'Successfully sent Checksum Scan Notification email alert to: %s' ), $sRecipient ) );
			}
			else {
				$this->addToAuditEntry( sprintf( _wpsf__( 'Failed to send Checksum Scan Notification email alert to: %s' ), $sRecipient ) );
			}
			return $bSendSuccess;
		}

		/**
		 * @return string
		 */
		protected function getCronName() {
			$oFO = $this->getFeatureOptions();
			return $oFO->prefixOptionKey( $oFO->getDefinition( 'corechecksum_cron_name' ) );
		}
	}

endif;