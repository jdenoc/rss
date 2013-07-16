/**
 * User: jdenoc
 * Created on: 2013-07-08
 * Last Modified: 2013-07-13
 */

$(document).ready(function(){
    if($('#feed_id').val() != ''){
        if($('#article').val() != ''){
            markRead( $('#article').val(), 0 );
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
            }
            endLoading();
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
            if(data == 0){
            // No articles available
                var empty = '<li style="padding: 60px 0; text-align: center;cursor: default;background: #e9e9e9;font-weight: bold; border-radius: 5px">Feed has nothing new at this moment...<br/><br/><br/>Sorry ( ; _ ; )</li>';
                $(empty).appendTo( $('#feed_list') );
            } else {        // Success
            // Display new articles
                $(data).appendTo( $('#feed_list') );
            }
            endLoading();
            assignListItemWidth();
        },
        error:function(){
            console.log('*** Error displaying Feed ***');
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
            if(data == 0){
            // Not a valid Article or Article unable to load
                var empty = '<li style="padding: 60px 0; text-align: center;cursor: default;background: #e9e9e9;font-weight: bold; border-radius: 5px">Article Not Available</li>';
                $(empty).appendTo( $('#feed_list') );
            } else {        // Success
            // Display Article
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
            }
            endLoading();

        },
        error:function(){
            console.log('*** Error displaying Article ***');
        }
    });
}


function markRead(article_id, isRead){
    $.ajax({
        type: 'POST',
        url: '../php_scripts/read_feed_article.php?m='+nocache(),
        data: {
            'viewed' : isRead,
            'article_id' : article_id
        },
        cache: false,
        beforeSend:function(){},
        success:function(data){
            // successful request;
            if(data != 0 && data != 1){     // Problems???
                // failed to Un-mark or mark article as read
                console.log('Problem marking article:'+article_id+' as read');
                console.log(data);
            }else{
                viewed = data;
            }
        },
        error:function(){
            console.log('*** Error Marking Article as Read ***');
        }
    });
}


function markArticle(article_id, isMarked){
    $.ajax({
        type: 'POST',
        url: '../php_scripts/mark_feed_article.php?m='+nocache(),
        data: {
            'marked' : isMarked,
            'article_id' : article_id
        },
        cache: false,
        beforeSend:function(){},
        success:function(data){
//          successful request;
            if(data == 0){          // Un-mark article
                $('#'+article_id+' .badge').removeClass('badge-warning');
                marked = 0;
                console.log('Article:'+article_id+' is now UN-marked.');

            }else if(data == 1){    // Mark article
                $('#'+article_id+' .badge').addClass('badge-warning');
                marked = 1;
                console.log('Article:'+article_id+' is NOW marked.');
//
            } else {                // Problem?
                console.log('Problem marking article:'+article_id+' as marked');
                console.log(data);
            }
        },
        error:function(){
            console.log('*** Error Marking Article for Later ***');
        }
    });
}