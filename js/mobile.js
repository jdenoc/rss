/**
 * Created with JetBrains PhpStorm.
 * User: denis
 * Date: 7/8/13
 * Time: 9:46 PM
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function(){
    if($('#feed_id').val() != ''){
        if($('#article').val() != ''){
            loadArticle( $('#article').val() );
        } else {
            loadFeed( $('#feed_id').val() );
        }
    } else {
        loadMenu();
    }

    $('.dropdown-toggle').dropdown();
});


$(document).resize( assignListItemWidth() );


function assignListItemWidth(){
    $('.list_item').css({width: ($(window).width() - 20)+'px'});
}


function nocache(){
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}


var isLoading = false;  // indicates if the loading screen from loading() is active.
function loading(){
    isLoading = true;
    // add the overlay with loading image to the page
    var over = '<div id="overlay">' +
        '<img id="loading" src="../img/loader.gif" alt="loading"/>' +
        '</div>';
    $(over).appendTo('body');

    // click on the overlay to remove it
    $('#overlay').click(function() {
        $(this).remove();
        isLoading = false;
    });

    // hit escape to close the overlay
    $(document).keyup(function(e) {
        if (e.which === 27) {
            $('#overlay').remove();
            isLoading = false;
        }
    });
}


function endLoading(){
    isLoading = false;
    $('#overlay').delay(250).queue(function(){
        $(this).remove();
        $(this).dequeue();
    });
}


function loadMenu(){
    $.ajax({
        type: 'POST',
        url: '../php_scripts/get_feed_menu.php?m='+nocache(),
        cache: false,
        beforeSend:function(){
            if(!isLoading)  loading();
        },
        success:function(data){
            // successful request
            if(data == 0){      // No feeds available
                alert('You currently havn\'t got any feeds right now. Why not click on the subscribe button.');
            }else{              // Feeds available and displayed
                $('#feed_list').append(data);
                endLoading();
            }
            assignListItemWidth();

        },
        error:function(){}
    });
}


function loadFeed(feed_id){
    $.ajax({
        type: 'POST',
        url: '../php_scripts/load_feed.php?m='+nocache(),
        data: {
            'feed_id' : feed_id
        },
        cache: false,
        beforeSend:function(){
            // remove articles currently displayed
            if(!isLoading)  loading();
        },
        success:function(data){
//          successful request
            if(data == 0){        // Not a valid RSS feed
                var empty = '<li style="padding: 60px 0; text-align: center;cursor: default;background: #e9e9e9;font-weight: bold; border-radius: 5px">Feed has nothing new at this moment...<br/><br/><br/>Sorry ( ; _ ; )</li>';
                $(empty).appendTo( $('#feed_list') );
                endLoading();
            } else {        // Success
            // display new articles
                $(data).appendTo( $('#feed_list') );
                endLoading();
            }
            assignListItemWidth();
        },
        error:function(){
            console.log('Error displaying Feed');
            endLoading();
        }
    });
}


function loadArticle(article_id){
    $.ajax({
        type: 'POST',
        url: '../php_scripts/load_article.php?m='+nocache(),
        data: {
            'article_id' : article_id
        },
        beforeSend:function(){
            console.log('Loading article: '+article_id);
            if(!isLoading)  loading();
        },
        success:function(data){
//          successful request
            if(data == 0){        // Not a valid Article;
                var empty = '<li style="padding: 60px 0; text-align: center;cursor: default;background: #e9e9e9;font-weight: bold; border-radius: 5px">Article Not Available</li>';
                $(empty).appendTo( $('#feed_list') );
                endLoading()
            } else {        // Success
//              display article
                console.log('loading article');
                $(data).appendTo( $('#feed_list') );
                $('.article').children().removeAttr('style');
                $('.article iframe').width( $('.article').width()-10 );
                $.each($('.article iframe'), function(){
                    var popout = '<br/><a href="'+$(this).attr('src')+'" title="Click to open in a new window" target="_blank">Popout</a><br/>';
                    $(this).after(popout);
                });
                $('.article a:link').attr('target', '_blank');      // All article links should open in a new tab.
                console.log('article loaded');
                endLoading();
            }

        },
        error:function(){
            console.log('Error displaying Article');
        }
    });
}