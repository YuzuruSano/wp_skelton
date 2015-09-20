(function($){
$(function(){
	$('.loop_wrapper').each(function(){
		var target_text,
			img_data;

		var target_array = [//上から優先的に採用
			{
				target : $(this).find('.cfs_file img'),
				type : 'file'
			},
			{
				target : $(this).find('.cfs_text'),
				type : 'text'
			},
			{
				target : $(this).find('.cfs_textarea'),
				type : 'textarea'
			},
			{
				target : $(this).find('.cfs_wysiwyg'),
				type : 'wysiwyg'
			}
		];

		for (i in target_array) {
			if(target_array[i].target.length >0){
				target_obj = target_array[i];
				var target_obj_type = target_obj.type;

				if(target_obj_type == 'text'){
					target_text = $('input',target_obj.target[0]).val();
					if(target_text){
						$(this).find('.cfs_loop_head span').first().text(target_text);
						break;
					}
				}

				if(target_obj_type == 'textarea' || target_obj_type == 'wysiwyg'){
					target_text = $('textarea',target_obj.target[0]).val();
					if(target_text){
						$(this).find('.cfs_loop_head span').first().text(target_text);
						break;
					}
				}

				if(target_obj_type == 'file'){
					img_data = $(target_obj.target[0]).clone();
					target_text = $(this).find('.cfs_text input').val();

					img_data.width(100).height(100).html();

					if(target_text){//1行テキストがあった場合テキストも追加
						$(this).find('.cfs_loop_head span').first().html('<p>'+target_text+'</p>').prepend(img_data);
					}else{
						$(this).find('.cfs_loop_head span').first().html(img_data);
					}
					break;
				}
			}
		}

	});
});
})(jQuery);