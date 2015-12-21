<!DOCTYPE html>
<html>
<head>
    <meta charset = "UTF-8">
    <title>Crawler By Name</title>
    <link rel="stylesheet" href="<?php echo base_url();?>asset/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>asset/css/font-awesome.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <hr>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Crawler by Name</h3>
                    </div>
                    <div class="panel-body">
                        <div class="input-group">
                            <input type="text" class="form-control url" placeholder="url from truyentranhtuan.com" >
                            <span class="input-group-btn">
                                <button class="btn btn-default exe" type="button">Executed!</button>
                            </span>
                        </div><!-- /input-group -->
                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
                <div>Doing: <span class="doing text-warning">Ready!</span></div>
                <div>=> Total images : <span class="test">0</span></div>
                <div>=> Insert success: <span class="insert">0</span></div>
                <hr>
                <div class="jumbotron">
                    <img src="" alt="" height="270px" width="200px">
                    <div class="content"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url();?>asset/js/jquery.min.js"></script>
    <script src="<?php echo base_url();?>asset/js/ajax.min.js"></script>
    <script>
    $(document).ready(function() {
        var data;
        var dt;
        var timeStart;
        var timeOut;
        var count = 0;
        function getImg (dt) {
            var str = String(dt);
            var pattern = /var slides_page_url_path = \[.*\]/;
            var result = str.match(pattern);
            if(result[0].length < 100){
                pattern = /var slides_page_path = \[.*\]/;
                result = str.match(pattern);
                pattern = /\[.*\]/;
                var result = result[0].match(pattern);
                var result = result[0].replace(/\"/gi, '');
                result = result.replace(/\[/gi, '');
                result = result.replace(/\]/gi, '');
                var r = result.split(',');
                for(var i = 0; i < r.length - 1; i++)
                    for(var j = i + 1; j< r.length; j++)
                        if(r[j] < r[i]){
                            var tmp = r[j];
                            r[j] = r[i];
                            r[i] = tmp;
                        }
                count += r.length;
                $('.test').text(count);
                return r.join();
            }
            pattern = /\[.*\]/;
            var result = result[0].match(pattern);
            var result = result[0].replace(/\"/gi, '');
            result = result.replace(/\[/gi, '');
            result = result.replace(/\]/gi, '');
            var r = result.split(',');
            count += r.length;
            $('.test').text(count);
            return result;
        }
        function doing (str) {
            $('.doing').text(str);
        }
        function proccess (dataChapter, story_id) {
            doing("Preparing get chapter");
            $('.panel-footer').append('<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div></div>');
            var total = dataChapter.length;
            var countDown = dataChapter.length;
            var cur = 0;
            var i = 0;
            var check = 0;
            $('.jumbotron .content').html("");
            if(total == 0) {
                $('.progress-bar').text(100 + "%");
                $('.progress-bar').attr('aria-valuenow', 100);
                $('.progress-bar').attr('style', "width: " + 100 + "%");
                $('.panel-footer').append('<h3 class="text-success text-center">SUCCESS</h3>');
                $('title').text('Crawler By Name');
                doing("URL wrong! exit(0)");
                return;
            }
            $(dataChapter).each(function(index, el) {
                    var a = $(this).find('a');
                    var aurl = a.attr('href');
                    var id = countDown--;
                    $.ajax({
                        url: aurl,
                        type: 'GET',
                        success: function(res) {
                            i++;
                            var chapter = $(res.responseText).find('#read-title p');
                            var pattern = /Chương .*/;
                            var result = chapter.text().match(pattern);
                            var chapter_title = $.trim(result[0]);
                            doing("Get images from chapter: " + a.text() + " - " + chapter_title +" (" + i+ "/ " + total + ")");
                            var img = getImg(res.responseText);
                            cur = cur + 100/total;
                            $('.progress-bar').text(Math.round(cur) + "%");
                            $('.progress-bar').attr('aria-valuenow', cur);
                            $('.progress-bar').attr('style', "width: " + cur + "%");
                            $('.cur').text(i)
                            $('title').text('Working ' + Math.round(cur) + "%");
                            $.post('<?php echo site_url();?>/ajax/insert/chapter', {
                                story: story_id,
                                img: img,
                                title: chapter_title
                            }, function(data, textStatus, xhr) {
                                if(textStatus == 'success' && data == 'TRUE') {
                                    check++;
                                    $('.insert').text(check + "/" + total);
                                    var currentdate = new Date();
                                    $('.jumbotron .content').prepend(chapter_title + " | " + a.text() +" => DONE | "+ currentdate.getHours() + ":" + currentdate.getMinutes() + ":" + currentdate.getSeconds() + "<br/>");
                                }
                            });
                            if(i == total) {
                                $('title').text('Crawler By Name');
                                timeOut = new Date().getTime();
                                $('.text-success').text("SUCCESS in " + (timeOut-timeStart) / 1000 + "s");
                                doing('Success!');
                            }
                        }
                      });
            });
            $('.panel-footer').append('<h3 class="text-success text-center">Working...(<span class="cur">0</span>/' + total + ')</h3>');
        }

        $('button.exe').click(function(event) {
            count = 0;
            timeStart = new Date().getTime();
            doing("Lấy và phân tích URL");
            $('.panel-footer').html("<img class='img-responsive center-block loading' src='<?php echo base_url();?>asset/images/loading.gif'/>");
            var i = 0;
            var url = $('input.url').val();
            if(url.length > 20){
                $('.panel-footer').append('<h3 class="text-center name">' + url + '</h3>');
                doing("Get list of chapter");
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        $('.loading').hide();
                        var dataStory = $(res.responseText).find('span.chapter-name');
                        var name = $(res.responseText).find('#infor-box h1');
                        var sumary = $(res.responseText).find('#infor-box #manga-summary');
                        var img = $(res.responseText).find('#infor-box .manga-cover img');
                        var img = img.attr('src');
                        var info = $(res.responseText).find('#infor-box .misc-infor');
                        //$('.jumbotron .content').html(sumary);
                        $('.name').text(name.text());
                        //$('.jumbotron img').attr("src", img.attr('src'));
                        $('.panel-footer').append('<h3>Total chapter: ' + dataStory.length + '</h3>');
                        $.post('<?php echo site_url();?>/ajax/insert/story', {
                            name: name.text(),
                            img: img,
                            sumary: sumary.html(),
                            realname: $(info[0]).text(),
                            author: $(info[1]).text(),
                            type: $(info[2]).text()
                        }, function(data, textStatus, xhr) {
                            if(textStatus == "success"){
                                proccess(dataStory, data);
                            }else
                            alert("ERROR! add Story");
                        });
                    }
                  });
            } else alert("URL wrong!");

        });

    });
    </script>
</body>
</html>