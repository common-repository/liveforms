<?php
global $ratingField;
$ratingField = false;
class Rating extends Field {

    public function field_id(){
        return get_class($this);
    }

	public function control_button() {
		ob_start();
		?>
		<li class="<?= $this->field_control_class; ?>" data-type="<?php echo __CLASS__ ?>" for="Rating">
			<span class="lfi lfi-name"><i class="fa fa-star"></i></span> Rating
			<a title="Rating" rel="Rating" class="add pull-right add-form-field" data-template='Rating' href="#"><i class="fas fa-plus-circle ttipf" title=""></i></a>
	    </li>
	    <?php
		$control_button_html = ob_get_clean();
		return $control_button_html;
	}

	public function field_preview_html($fieldindex, $fieldid, $field_infos) {
		ob_start();
		?>
		<div class='form-group'>
            <i class="fa fa-star text-success"></i>
            <i class="fa fa-star text-success"></i>
            <i class="fa fa-star text-success"></i>
            <i class="fa fa-star text-success"></i>
            <i class="fa fa-star text-success"></i>
		</div>
		<?php
        $preview = ob_get_clean();
        ob_start();
        include LF_BASE_DIR.'views/field-settings/field-preview.php';
        $field_render_html = ob_get_clean();
        return $field_render_html;
	}

	public function field_html($params = array()) {
        global $ratingField;
		ob_start();
        $default_value = \LiveForms\__\__::valueof($params, 'default_value');
        $default_value = $this->parse_var($default_value, $params);

	    ?>
			<div class='form-group'>
                <div id="rateYo_<?php echo $params['id'] ?>" class="rateyo" data-target="#<?php echo $params['id'] ?>_rate"></div>
                <input type="hidden" name="submitform[<?php echo $params['id'] ?>]" id="<?php echo $params['id'] ?>_rate">
            </div>
        <?php  if(!$ratingField) { ?>
            <script>
            jQuery(function ($){
                $('<link/>', {rel: 'stylesheet', href: "<?= LF_ASSET_URL; ?>rateyo/jquery.rateyo.min.css"}).appendTo('head');
                $.getScript("<?= LF_ASSET_URL; ?>rateyo/jquery.rateyo.min.js", function (){
                    $('#formarea .rateyo').each(function (){
                        var rateyo = $(this).rateYo({
                            rating: <?= (double)$default_value; ?>,
                            normalFill: '#dddddd',
                            spacing: "2px",
                            starSvg: '<svg height="511pt" viewBox="0 -10 511.98685 511" width="511pt" xmlns="http://www.w3.org/2000/svg"><path d="m510.652344 185.902344c-3.351563-10.367188-12.546875-17.730469-23.425782-18.710938l-147.773437-13.417968-58.433594-136.769532c-4.308593-10.023437-14.121093-16.511718-25.023437-16.511718s-20.714844 6.488281-25.023438 16.535156l-58.433594 136.746094-147.796874 13.417968c-10.859376 1.003906-20.03125 8.34375-23.402344 18.710938-3.371094 10.367187-.257813 21.738281 7.957031 28.90625l111.699219 97.960937-32.9375 145.089844c-2.410156 10.667969 1.730468 21.695313 10.582031 28.09375 4.757813 3.4375 10.324219 5.1875 15.9375 5.1875 4.839844 0 9.640625-1.304687 13.949219-3.882813l127.46875-76.183593 127.421875 76.183593c9.324219 5.609376 21.078125 5.097657 29.910156-1.304687 8.855469-6.417969 12.992187-17.449219 10.582031-28.09375l-32.9375-145.089844 111.699219-97.941406c8.214844-7.1875 11.351563-18.539063 7.980469-28.925781zm0 0" /></svg>',
                            multiColor: {
                                "startColor": "#ff1b1b", //RED
                                "endColor"  : "#25bf49"  //GREEN
                            },
                            ratedFill: "#25bf49"
                        });
                        $(this).on('click', function (){
                            $($(this).data('target')).val($(this).rateYo('rating'));
                        });
                    });
                });

            });

            </script>
        <?php }  ?>
		<?php
		$field_html = ob_get_clean();
        $ratingField = true;
		return $field_html;
	}

	function process_field() {

	}

}
