$(document).ready(function(){
    /* Jquery unveil */
    $("img").unveil(200);
    
    /* Posts sliders */
    function nextSlide (slideId){
        if (slideId.find('.post').length > 1){
            var currentSlide = slideId.find('.active-post');
            if (currentSlide.is(slideId.find('.post:last'))) {
                slideId.find('.post:first-child').addClass('active-post');
                slideId.find('.post:first-child > img').trigger("unveil");
                $('#children-'+slideId.find('.post:first-child').data('post-id')).addClass('active-group');
            } else {
                currentSlide.next().addClass('active-post');
                currentSlide.next().find('img').trigger("unveil");
                $('#children-'+currentSlide.next().data('post-id')).addClass('active-group');
            }
            currentSlide.removeClass('active-post');
            $('#children-'+currentSlide.data('post-id')).removeClass('active-group');
            slideChange();
        }
    }
    
    function prevSlide (slideId){
        if (slideId.find('.post').length > 1){
            var currentSlide = slideId.find('.active-post');
            if (currentSlide.is(slideId.find('.post:first-child'))){
                slideId.find('.post:last').addClass('active-post');
                slideId.find('.post:last > img').trigger("unveil");
                $('#children-'+slideId.find('.post:last').data('post-id')).addClass('active-group');
            } else {
                currentSlide.prev().addClass('active-post');
                currentSlide.prev().find('img').trigger("unveil");
                $('#children-'+currentSlide.prev().data('post-id')).addClass('active-group');
            }
            currentSlide.removeClass('active-post');
            $('#children-'+currentSlide.data('post-id')).removeClass('active-group');
            slideChange();
        }
    }
    
    $(".slideshow-container").on("swipeleft",function(){ nextSlide($(this)); });
    $(document).on("click",".next",function(){ nextSlide($(this).parent()); $(this).closest('.slideshow-label').trigger('click'); });

    $(".slideshow-container").on("swiperight",function(){ prevSlide($(this)); });
    $(document).on("click",".prev",function(){ prevSlide($(this).parent()); $(this).closest('.slideshow-label').trigger('click'); });
    
    /* form input preview */
    $('#add-post-input').on('change', function(){
        if (this.files && this.files[0] && this.value != '') {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#post-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
            $('#post-preview').show();
        } else {
            $('#post-preview').hide();
        }
    });
    
    /* Document smooth height change */
    function slideChange () {
        $('#player-content').attr('style', 'height: '+$('#player-content div:first-child').css('height')+';');
    }
    slideChange();
});