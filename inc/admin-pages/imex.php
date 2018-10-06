<div class="wrap">
	<h2><?php _e( 'Hide Admin Menu - Import & Export', 'ham' ); ?></h2>

	<h3><?php _e( 'Export', 'ham' ); ?></h3>

	<form method="post" action="">
		<?php wp_nonce_field( 'ham-export' ); ?>
		<p><?php _e( 'Click the button below to download setting file. You can import it later on this site or on another site.', 'ham' ); ?></p>

		<?php submit_button( __( 'Export Settings', 'ham' ), 'primary', 'export' ); ?>
	</form>

	<h3><?php _e( 'Import', 'ham' ); ?></h3>

	<form method="post" action="" enctype="multipart/form-data">
		<?php wp_nonce_field( 'ham-import' ); ?>
		<p><?php _e( 'Choose exported setting file and click "Import Settings" to start importing.', 'ham' ); ?></p>

		<p><input type="file" name="setting"/></p>

		<?php submit_button( __( 'Import Settings', 'ham' ), 'primary', 'import' ); ?>
	</form>
</div>
