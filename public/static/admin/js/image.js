/**
 * Created by Administrator on 2018/3/4.
 */
$(function() {
    // 会员增加-图片
    $("#file_upload1").uploadify({
        swf           : swf,
        uploader      : image_upload_url,
        buttonText:'上传图片',
        fileTypeDesc:'Image Files',
        fileObjName:'file',
        fileTypeExts:'*.gif;*.jpg;*.png;*.bmp;*.jpeg',
        onUploadSuccess:function(file, data, response) {
            var obj = JSON.parse(data);
            if (obj.status!=1) {
                layer.msg(obj.message);
            } else {
                $('#upload_img1').attr('src', obj.data);
                $('#upload_img1').show();
                $('#file_upload_img1').attr('value', obj.data);
            }
            // layer.msg(file.id);
            var cancel=$("#"+file.id + " .cancel a");
            if (cancel) {
                cancel.click(function(){
                    $('#upload_img1').attr('src', '');
                    $('#upload_img1').hide();
                    $('#file_upload_img1').attr('value', '');
                });
            }
        },
        onCancel:function(file) {
            $('#upload_img1').attr('src', '');
            $('#upload_img1').hide();
            $('#file_upload_img1').attr('value', '');
        }
    });
    $("#file_upload2").uploadify({
        swf           : swf,
        uploader      : image_upload_url,
        buttonText:'上传图片',
        fileTypeDesc:'Image Files',
        fileObjName:'file',
        fileTypeExts:'*.gif;*.jpg;*.png;*.bmp;*.jpeg',
        onUploadSuccess:function(file, data, response) {
            var obj = JSON.parse(data);
            if (obj.status!=1) {
                layer.msg(obj.message);
            } else {
                $('#upload_img2').attr('src', obj.data);
                $('#upload_img2').show();
                $('#file_upload_img2').attr('value', obj.data);
            }
            // layer.msg(file.id);
            var cancel=$("#"+file.id + " .cancel a");
            if (cancel) {
                cancel.click(function(){
                    $('#upload_img2').attr('src', '');
                    $('#upload_img2').hide();
                    $('#file_upload_img2').attr('value', '');
                });
            }
        },
        onCancel:function(file) {
            $('#upload_img2').attr('src', '');
            $('#upload_img2').hide();
            $('#file_upload_img2').attr('value', '');
        }
    });
    $("#file_upload3").uploadify({
        swf           : swf,
        uploader      : image_upload_url,
        buttonText:'上传图片',
        fileTypeDesc:'Image Files',
        fileObjName:'file',
        fileTypeExts:'*.gif;*.jpg;*.png;*.bmp;*.jpeg',
        onUploadSuccess:function(file, data, response) {
            var obj = JSON.parse(data);
            if (obj.status!=1) {
                layer.msg(obj.message);
            } else {
                $('#upload_img3').attr('src', obj.data);
                $('#upload_img3').show();
                $('#file_upload_img3').attr('value', obj.data);
            }

            // layer.msg(file.id);
            var cancel=$("#"+file.id + " .cancel a");
            if (cancel) {
                cancel.click(function(){
                    $('#upload_img3').attr('src', '');
                    $('#upload_img3').hide();
                    $('#file_upload_img3').attr('value', '');
                });
            }
        },
        onCancel:function(file) {
            $('#upload_img3').attr('src', '');
            $('#upload_img3').hide();
            $('#file_upload_img3').attr('value', '');
        }
    });
    $("#file_upload4").uploadify({
        swf           : swf,
        uploader      : image_upload_url,
        buttonText:'上传图片',
        fileTypeDesc:'Image Files',
        fileObjName:'file',
        fileTypeExts:'*.gif;*.jpg;*.png;*.bmp;*.jpeg',
        onUploadSuccess:function(file, data, response) {
            var obj = JSON.parse(data);
            if (obj.status!=1) {
                layer.msg(obj.message);
            } else {
                $('#upload_img4').attr('src', obj.data);
                $('#upload_img4').show();
                $('#file_upload_img4').attr('value', obj.data);
            }
            // layer.msg(file.id);
            var cancel=$("#"+file.id + " .cancel a");
            if (cancel) {
                cancel.click(function(){
                    $('#upload_img4').attr('src', '');
                    $('#upload_img4').hide();
                    $('#file_upload_img4').attr('value', '');
                });
            }
        },
        onCancel:function(file) {
            $('#upload_img4').attr('src', '');
            $('#upload_img4').hide();
            $('#file_upload_img4').attr('value', '');
        }
    });
    $("#file_upload0").uploadify({
        swf           : swf,
        uploader      : image_upload_url,
        buttonText:'上传图片',
        fileTypeDesc:'Image Files',
        fileObjName:'file',
        fileTypeExts:'*.gif;*.jpg;*.png;*.bmp;*.jpeg',
        onUploadSuccess:function(file, data, response) {
            var obj = JSON.parse(data);
            if (obj.status!=1) {
                layer.msg(obj.message);
            } else {
                $('#upload_img0').attr('src', obj.data);
                $('#upload_img0').show();
                $('#file_upload_img0').attr('value', obj.data);
            }
            // layer.msg(file.id);
            var cancel=$("#"+file.id + " .cancel a");
            if (cancel) {
                cancel.click(function(){
                    $('#upload_img0').attr('src', '');
                    $('#upload_img0').hide();
                    $('#file_upload_img0').attr('value', '');
                });
            }
        },
        onCancel:function(file) {
            $('#upload_img0').attr('src', '');
            $('#upload_img0').hide();
            $('#file_upload_img0').attr('value', '');
        }
    });
});