define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var ImageCrop = require('edusoho.imagecrop');

    exports.run = function() {

        var imageCrop = new ImageCrop({
            element: "#avatar-crop",
            group: "user",
            cropedWidth: 200,
            cropedHeight: 200
        });

        imageCrop.on("afterCrop", function(response) {
            var url = $("#upload-avatar-btn").data("url");
            $.post(url, {
                images: response
            }, function(response) {
                if (response.status === 'success') {
                    $("#profile_avatar").val(response.avatar);
                    $("#user-profile-form img").attr('src', response.avatar);
                    $("#profile_avatar").blur();
                    $("#modal").modal('hide');
                    Notify.success('上传成功');
                } else {
                    Notify.success('上传失败,请重试');
                }
            });
        });

        $("#upload-avatar-btn").click(function(e) {
            e.stopPropagation();

            imageCrop.crop({
                imgs: {
                    large: [200, 200],
                    medium: [120, 120],
                    small: [48, 48]
                }
            });

        })
    };

});