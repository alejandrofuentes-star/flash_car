<footer>
	<div class="w_100 d_flex_center_center bg_color_background">
		<div class="w_90 d_flex_center_center p_2">
			<a class="button_custom_footer bg_telefono_footer" title="abrir app para hacer llamadas" href="tel:+52{{ App\Models\SiteSetting::get('telefono') }}" target="_self"><i class="bi bi-telephone-forward-fill"></i></a>
			<a class="button_custom_footer bg_whatsapp_footer" title="abrir app para mandar mensajes" href="https://wa.me/+52{{ App\Models\SiteSetting::get('whatsapp') }}?text=Me interesa conocer más sobre sus rentas" target="_blank"><i class="bi bi-whatsapp"></i></a>
		</div>
		<div class="w_90 d_flex_center_between border_top_custom p_2">
			<div class="w_50 d_flex_center_center_column">
				<p class="text_color_white p_1 footer_img_label">{{ __('footer.created_by') }}</p>
				<a href="https://neructechnologies.com" target="_blank"><img src="{{ asset('./img/tech-w.webp') }}" class="footer_img_tech" alt="logo neruc technologies"></a>
			</div>
			<div class="w_50 d_flex_center_center_column">
				<p class="text_color_white p_1 footer_img_label">{{ __('footer.marketing') }}</p>
				<a href="https://nerucmarketing.com" target="_blank"><img src="{{ asset('./img/marketing_w.webp') }}" class="footer_img_marketing" alt="logo neruc marketing"></a>
			</div>
		</div>
	</div>
</footer>
