<?php
require __DIR__ . '/../vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
        z-index: 0;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      .hidden {
        display: none;
      }
      #login {
        position: absolute;
        top: 50%;
        left: calc(50% - 70px);
        width: 140px;
        text-align: center;
        z-index: 9999;
        background: #00d1b2;
        border-radius: 8px;
      }
      #login a {
        padding: 20px;
        display: block;
        text-decoration: none;
        color: white;
        font-size: 18px;
        line-height: 22px;
        font-family: sans-serif;
      }
      #login a:hover {
        background: #0abda3;
        border-radius: 8px;
      }
      #video {
        position: absolute;
        top: 50%;
        left: calc(50% - 160px);
        width: 400px;
        text-align: center;
        z-index: 9999;
        background: #00d1b2;
        border-radius: 8px;
        padding: 10px;
      }
      #video span {
        width: 100%;
        display: flex;
        flex-direction: row;
      }
      #video input {
        font-size: 2rem;
        width: 100%;
        height: 100%;
        border: 1px #ccc solid;
        border-radius: 4px;
        margin-left: 8px;
      }
      #video-url {
        flex: 3;
      }
      #set-video-url {
        flex: 1;
      }

      highlight-chat {
        font-family: Avenir Next;
        font-weight: 600;
        box-sizing: border-box;
        display: block;
        position: absolute;
        bottom: 10px;
        left:0;
        width: 100%;
        height: 300px;
        z-index:99999999999;
        overflow: hidden;
        margin: 0px;
        padding: 40px 50px 40px 220px;
        color: #fff;
        font-size: 30px;
    }

    .hl-c-cont {
        position: relative;
        padding: 20px;
        width: 100%;
        margin: 0 auto;
        transition: .5s all cubic-bezier(0.250, 0.250, 0.105, 1.2);
    }
    .hl-c-cont.fadeout {
        transform: translateY(600px);
    }

    .hl-name {
        position: absolute;
        top: -20px;
        left: 50px;
        font-weight: 700;
        background: #ffa500;
        color: #444;
        padding: 10px;
        transform: rotate(-0deg);
        z-index: 1;
    }
    .hl-message {
        position: absolute;
        font-size: 45px;
        font-weight: 600;
        padding: 20px 40px 20px 70px;
        background-color: #222; /* needs to be slightly above black to not get keyed out */
    }
    .hl-message img {
        width: 50px;
        vertical-align: middle;
    }
    .hl-img {
        position: absolute;
        top: 0;
        z-index: 1;
        left: -60px;
        width: 128px;
        height: 128px;
        background-color: orange;
        border-radius: 50%;
        border: 0;
        padding: 3px;
    }
    .hl-img img {
        display: block;
        width: 100%;
        border-radius: 50%;
        z-index: 1;
    }

    </style>
</head>
<body>

<div id="map"></div>
<div id="login" class="hidden"><a href="/login.php?go">Connect YouTube</a></div>
<div id="video" class="hidden">
    <span>
        <input type="url" id="video-url" placeholder="youtube video url">
        <input type="button" value="Set" id="set-video-url">
    </span>
</div>
<highlight-chat></highlight-chat>

<script src="jquery-3.5.1.min.js"></script>
<script>
var map;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 21, lng: 16},
        zoom: 3,
        disableDefaultUI: true,
        mapTypeId: 'hybrid'
    });

    checkLogin();
}

function checkLogin() {
    $.post("/login.php?check", function(data){
        if(data.loggedin == false) {
            showLoginForm();
        } else {
            refreshChat();
            pollForNewMessages();          
        }
    });
}

function refreshChat() {
    $.post("/chat.php", function(data){
        console.log(data);
        if(data.error == 'no-chat') {
            $("#video").removeClass("hidden");
            return;
        }

        if(data.chat) {
            findPlace(data.chat);
        }

        setTimeout(refreshChat, 2000);
    });
}

function pollForNewMessages() {
    $.post("/poll.php", function(data){
        console.log(data);
        if(data.error == 'no-chat') {
            $("#video").removeClass("hidden");
            return;
        }

        if(data.interval) {
            setTimeout(pollForNewMessages, data.interval);
        }
    });
}

function findPlace(chat) {
  var request = {
    query: chat.location,
    fields: ['name', 'geometry'],
  };

  var service = new google.maps.places.PlacesService(map);

  service.findPlaceFromQuery(request, function(results, status) {
    if (status === google.maps.places.PlacesServiceStatus.OK) {
        console.log(results[0]);
        new google.maps.Marker({position: results[0].geometry.location, map: map});
        showChatMessage(data.chat);
    } else {
        console.log(results);
        return null;
    }
  });
}

function showLoginForm() {
    $("#login").removeClass("hidden");
}

var hideTimeout;
function showChatMessage(chat) {
    $("highlight-chat").html('<div class="hl-c-cont fadeout"><div class="hl-name">' + chat.author_name + '</div>' + '<div class="hl-message">' + chat.message + '</div><div class="hl-img"><img src="' + chat.author_photo + '"></div></div>')
    .delay(10).queue(function(next){    
        $( ".hl-c-cont" ).removeClass("fadeout");
        next();
    }); 

    if(hideTimeout) {
        clearTimeout(hideTimeout);
    }

    hideTimeout = setTimeout(function(){
        $("highlight-chat .hl-c-cont").addClass("fadeout");
    }, 5000);
}

$(function(){
    $("#set-video-url").click(function(){
        $.post("/set-active-video.php", {
            video: $("#video-url").val()
        }, function(data){
            console.log(data);
            if(data.result == 'ok') {
                $("#video").addClass("hidden");
                refreshChat();
                pollForNewMessages();
            } else {
                $("#video-url").val("");
            }
        });
    });
});

</script>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?= $_ENV['GOOGLEMAPS_APIKEY'] ?>&callback=initMap" async defer></script>

</body>
</html>