/**
 * User: denis
 * Date: 6/12/13
 */

$(document).ready(function(){
    loadMenuCol();
    $('.dropdown-toggle').dropdown();
});

$(window).resize( function(){
    setArticleStampLeft();
    $('.article iframe').width( $('.article').width()-10 );
});

function nocache(){
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}


function subscribeToFeed(){
    var url = $('#feed_url').val();

    $.ajax({
        type: 'POST',
        url: './includes/create_subscription.php?x='+nocache(),
        data: {
            'url' : url
        },
        cache: false,
        beforeSend:function(){
            if(!isLoading)   loading();
        },
        success:function(data){
            // successful request
            if (data == -1){            // Didn't pass URL
                $('#feed_url').css('backgroundColor', '#F77');
                $('#create_subscription_notice').html('You did not enter a value either feed URL');
                endLoading();

            }else if(data == 0){        // Not a valid RSS feed
                $('#feed_url').css('backgroundColor', '#F77');
                $('#create_subscription_notice').html('Feed URL is not valid');
                endLoading();

            }else if(data == 1){        // Success
                $('#create_subscription_notice').html('SUCCESS!');
                finishSubscriptionToFeed(url);

            }else if(data == 2){        // Already exists
                $('#create_subscription_notice').html('Feed already exists');
                endLoading();

            } else {                    // Some sort of PHP error
                $('#create_subscription_notice').html('An error has occurred while subscribing to a feed: '+url);
                endLoading();

            }
        },
        error:function(){
           $('#create_subscription_notice').html('An error occurred while subscribing to the feed: '+url);
            endLoading();
        }
    });
}


function finishSubscriptionToFeed(url){
    $.ajax({
        type: 'POST',
        url: './includes/create_subscription.php?x='+nocache()+'&json='+nocache(),
        data: {
            'url' : url
        },
        cache: false,
        beforeSend:function(){
            if(!isLoading)   loading();
        },
        success:function(data){
            // successful request
            var subscriptionInfo = $.parseJSON(data);
            loadRss(subscriptionInfo.feed_title, subscriptionInfo.id);
            loadMenuCol();
        },
        error:function(){
            $('#create_subscription_notice').html('An error occurred while subscribing to the feed: '+url);
            endLoading();
        }
    });
}


function showSubscribeToFeed(){
    if($('#subscribe_form').is(':hidden')){
        $('#subscribe_form').slideDown();
    } else {
        $('#subscribe_form').slideUp();
    }
}


function cancelSubscribeToFeed(){
    $('#feed_url').val('');
    $('#feed_title').val('');
    $('#subscribe_form').slideUp();
}


function loadMenuCol(){
    $.ajax({
        type: 'POST',
        url: './includes/get_feed_menu.php?x='+nocache(),
        cache: false,
        beforeSend:function(){
            displayMarkedCount();
        },
        success:function(data){
            // successful request; do something with the data
            if(data == 0){      // No feeds available
                alert('You currently havn\'t got any feeds right now. Why not click on the subscribe button.');
            }else{              // Feeds available and displayed
                $('#menu_feeds_list').find('.menu_feed_list_item').remove();
                $('#menu_feeds_list').append(data);
                setTimeout(loadMenuCol, 10*1000);
            }

        },
        error:function(){}
    });
}


function displayMarkedCount(){
    $.ajax({
        type: 'POST',
        url: './includes/get_feed_menu.php?marked='+nocache()+'&x='+nocache(),
        cache: false,
        beforeSend:function(){},
        success:function(data){
            if(!isNaN(parseInt(data)))  // Returned data was a number
                $('#marked_count').html('('+parseInt(data)+')');
            else                        // Returned data didn't contain an int
                $('#marked_count').html('');
        },
        error:function(){}
    });
}


function removeSubscription(feedID){
    var confirmMsg = 'Are you sure you want to delete this feed?\nDoing so will delete all related articles, mark or not.';
    if(!confirm(confirmMsg)){
        return;
    }
    $.ajax({
        type: 'POST',
        url: './includes/remove_subscription.php?x='+nocache(),
        data: {
            'feed_id' : feedID
        },
        cache: false,
        beforeSend:function(){},
        success:function(data){
            // successful request; do something with the data
            loadMenuCol();
            if(data == 0){
                alert('Unable to remove feed');
            }
        },
        error:function(){}
    });
}