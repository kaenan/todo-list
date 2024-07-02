<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{csrf_token()}}">
        <script type="text/javascript" src="{{ URL::asset('assets/js/jQuery.js') }}"></script>
        <link rel="stylesheet" href="{{ URL::asset('assets/css/index.css') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- <script type="text/javascript" src="{{ URL::asset('assets/js/listItem.js') }}"></script> -->
        <title>Laravel</title>
    </head>
    <body>

        <div id="container">
            <div id="sidebar">
                <div id="sidebar-container">
                    <p>Listz</p>
                    <p>Archived</p>
                </div>
                <div id="sidebar-opener">
                    <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
                </div>
            </div>

            <div id="body-container">
                <div>
                    <h1>My TODO Lists.</h1>
                    <form action="{{ route('createList') }}" method="post">
                        {{csrf_field()}}
                        <label for="list_name">List name:</label>
                        <input type="text" name="list_name" id="list_name_input">
                        <button>Create</button>
                    </form>
                </div>

                <div style="padding: 20px; display: flex;">
                    @foreach ($lists as $list)
                        <div id="list-container-{{$list->id}}" class="list-container">
                            <div id="list-{{$list->id}}" class="list">
                                <div class="list-header-container">
                                    <h3 id="list-title-{{$list->id}}" class="list-title">{{$list->name}}</h3>
                                    <i class="fa fa-eye list-archive-button" data-listid="{{$list->id}}"></i>
                                </div>
                                @foreach ($list->items as $item)
                                    <div id="item-{{$item->id}}-container" class="listitem-{{$list->id}}-container listitem-container">
                                        <div class="listitem-title" data-itemid="{{$item->id}}">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                            <p id="item-{{$item->id}}" class="list-item list-{{$list->id}}-item {{$item->completed}}" data-original="{{$item->name}}">{{$item->name}}</p>
                                        </div>
                                        <input type="checkbox" name="complete" class="item-check" data-itemid="{{$item->id}}" {{$item->completed}}>
                                    </div>
                                @endforeach
                                <input type="text" id="add-item-input-{{$list->id}}" class="add_item_input" data-listid="{{$list->id}}" placeholder="New list item..." autocomplete="off">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </body>
</html>

<script>
    $(document).ready(function() {

        const input_name = "add_item_input";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            }
        });

        var sidebarCookie = getCookie('sidebar');
        if (sidebarCookie) {
            $('#sidebar').addClass(sidebarCookie);
        } else {
            $('#sidebar').addClass('sidebar-closed');
        }

        $('#sidebar-opener').click(function() {
            if ($('#sidebar').hasClass('sidebar-open')) {
                $('#sidebar').removeClass('sidebar-open');
                $('#sidebar').addClass('sidebar-closed');
                setCookie('sidebar', 'sidebar-closed', 1);
            } else {
                $('#sidebar').addClass('sidebar-open');
                $('#sidebar').removeClass('sidebar-closed');
                setCookie('sidebar', 'sidebar-open', 1);
            }
        })

        $(`.${input_name}`).each(function() {
            ajaxSaveItem($(this));
        });

        $('.list-archive-button').each(function() {
            $input = $(this);
            var listid = $input.data('listid');
            $input.on('click', function() {
                ajaxArchiveList(listid);
            });
        });

        $('.item-check').each(function() {
            ajaxCompleteItem($(this));
        });

        $('.listitem-title').each(function() {
            ajaxDeleteItem($(this));
        });

        function ajaxSaveItem($input) {
            var listid = $input.data('listid');
            $input.change(function() {
                $.ajax({
                    url: "{{url('/saveItem')}}",
                    method: 'POST',
                    data: {
                        listid: listid,
                        name: $input.val()
                    },
                    success: function(data) {
                        refesh_list(listid, data, $input);
                    }
                });
            })
        }

        function refesh_list(listid, data, $input) {
            var $response = $(data);
            $input.val('');

            $(`.listitem-${listid}-container`).remove();
            $(`#add-item-input-${listid}`).remove();

            $.each($response.get(0), function(i, obj) {
                var itemid = obj.id
                var name = obj.name;
                var complete = obj.complete ? 'checked' : '';
                $(`#list-${listid}`).append(`<div id="item-${itemid}-container" class="listitem-${listid}-container listitem-container">`);
                $(`#item-${itemid}-container`).append(`<div id="listitem-title-${itemid}" class="listitem-title">`);
                $(`#listitem-title-${itemid}`).append('<i class="fa fa-trash" aria-hidden="true"></i>');
                $(`#listitem-title-${itemid}`).append(`<p id="item-${itemid}" class="list-item list-${listid}-item ${complete}">${name}</p>`);
                $(`#item-${itemid}-container`).append(`<input type="checkbox" name="complete" id="item-check-${itemid}" class="item-check" data-itemid="${itemid}" ${complete}>`);
                ajaxCompleteItem($(`#item-check-${itemid}`));
            });
            $(`#list-${listid}`).append(`<input type="text" id="add-item-input-${listid}" class="add_item_input" data-listid="${listid}" placeholder="New list item..." autocomplete="off">`);

            ajaxSaveItem($(`#add-item-input-${listid}`));
        }

        function ajaxArchiveList(listid) {
            $.ajax({
                    url: "{{url('/archiveList')}}",
                    method: 'POST',
                    data: {
                        listid: listid
                    },
                    success: function(data) {
                        $(`#list-container-${listid}`).remove();
                    }
            });
        }

        function ajaxCompleteItem($input) {
            if ($input.click(function() {
                var itemid = $input.data('itemid');
                var complete = 0;
                if($(this).prop("checked") == true) {
                    complete = 1;
                }
                $.ajax({
                    url: "{{url('/markComplete')}}",
                        method: 'POST',
                        data: {
                            id: itemid,
                            complete: complete
                        },
                        success: function(data) {
                            if (complete) {
                                $(`#item-${itemid}`).addClass('checked');
                            } else {
                                $(`#item-${itemid}`).removeClass('checked');
                            }
                        }
                });
            }));
        }

        function ajaxDeleteItem($input) {
            var itemid = $input.data('itemid');
            var itemEl = `#item-${itemid}-container`;
            $input.click(function() {
                $.ajax({
                    url: "{{url('/deleteItem')}}",
                    method: "POST",
                    data: {
                        itemid: itemid
                    },
                    success: function() {
                        $(itemEl).remove();
                    }
                })
            })
        }

        function getCookie(name) {
        // Split all cookies into an array of key-value pairs
        var cookies = document.cookie.split(';');

        // Loop through each cookie
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();

            // Check if the cookie starts with the provided name
            if (cookie.indexOf(name + '=') === 0) {
            // Return the cookie value (substring after the '=' sign)
            return cookie.substring(name.length + 1);
            }
        }

        // If the cookie with the provided name is not found, return null
        return null;
        }

        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        }
    })
</script>
