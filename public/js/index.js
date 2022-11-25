const layer = layui.layer
const $ = layui.$
const show = (data) => {
    const loading = layer.open({
        type: 3
    })

    $.ajax({
        type: "get",
        dataType: "json",
        url: `/know-that/laravel-logger/contents`,
        async: true,
        data: data,
        success: (data) => {
            layer.open({
                type: 1,
                shadeClose: true,
                title: data.name,
                content: data.contents
            })
        },
        error: (data) => {
            layer.msg(data?.responseJSON?.message || '接口异常', {
                offset: [30]
            })
        }
    })

    layer.close(loading)
}
