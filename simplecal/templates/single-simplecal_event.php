<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|70"}}}} -->
<main class="wp-block-group">
	<!-- wp:group {"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:post-title {"textAlign":"center","level":1} /-->

		<!-- wp:spacer {"height":"var:preset|spacing|30","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
		<div style="margin-top:0;margin-bottom:0;height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:post-featured-image {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} /-->

		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:html -->
				<div class="simplecal_list_item_meta_icon"><span class="material-symbols-outlined">calendar_month</span></div>
				<!-- /wp:html -->

				<!-- wp:heading {"level":"2"} -->
				<h2 class="wp-block-heading">
					<!-- wp:simplecal/meta-block {"metaField":"eventStartDateTime"} /-->
				</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:html -->
				<div class="simplecal_list_item_meta_icon"><span class="material-symbols-outlined">schedule</span></div>
				<!-- /wp:html -->

				<!-- wp:heading {"level":"2"} -->
				<h2 class="wp-block-heading">
					<!-- wp:simplecal/meta-block {"metaField":"eventEndDateTime"} /-->
				</h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:html -->
				<div class="simplecal_list_item_meta_icon"><span class="material-symbols-outlined">pin_drop</span></div>
				<!-- /wp:html -->

				<!-- wp:heading {"level":"3"} -->
				<h3 class="wp-block-heading">
					<!-- wp:simplecal/meta-block {"metaField":"eventFullAddress","linkType":"after"} /-->
				</h3>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:html -->
				<div class="simplecal_list_item_meta_icon"><span class="material-symbols-outlined">camera_video</span></div>
				<!-- /wp:html -->

				<!-- wp:heading {"level":"3"} -->
				<h3 class="wp-block-heading"><!-- wp:simplecal/meta-block {"metaField":"eventMeetingLink"} /--></h3>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:html -->
				<div class="simplecal_list_item_meta_icon"><span class="material-symbols-outlined">link</span></div>
				<!-- /wp:html -->
		
				<!-- wp:heading {"level":"3"} -->
				<h3>
					<!-- wp:simplecal/meta-block {"metaField":"eventWebsite"} /-->
				</h3>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
		 
		 <!-- wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
		 <hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)"/>
		 <!-- /wp:separator -->
	</div>
	<!-- /wp:group -->


	<!-- wp:post-content {"lock":{"move":false,"remove":true},"layout":{"type":"constrained"}} /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /-->