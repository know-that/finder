<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/vendor/kt.logger/layui/css/layui.css">
    <link rel="stylesheet" href="/vendor/kt.logger/iconfont/iconfont.css">
    <link rel="stylesheet" href="/vendor/kt.logger/css/index.css">
</head>
<body style="padding: 50px">
    <div class="layui-breadcrumb">
        @foreach($locations as $location)
            <a href="?path={{$location['url']}}">{{ $location['name'] }}</a>
        @endforeach
    </div>

    <table class="layui-table">
        <thead>
            <tr>
                <th>名称</th>
                <th>类型</th>
                <th>大小</th>
                <th>修改日期</th>
            </tr>
        </thead>
        <tbody>
        @if(count($data) > 0)
        @foreach($data as $item)
            <tr>
                <td class="name">
                    @if($item['type'] === 'dir')
                        <a class="layui-font-blue" href="?path={{$item['path'] }}">
                            <i class="iconfont icon-folder"></i> {{ $item['name']  }}
                        </a>
                    @else
                        <a class="layui-font-blue" href="javascript:;" onclick="show({
                            path: '{{ $item['relative_path'] }}',
                            name: '{{ $item['name'] }}'
                        })">
                            <i class="iconfont icon-file"></i> {{ $item['name']  }}
                        </a>
                    @endif
                </td>
                <td>{{ $item['type_text']  }}</td>
                <td>{{ $item['size_text']  }}</td>
                <td>{{ $item['m_time']  }}</td>
            </tr>
        @endforeach
        @else
            <tr>
                <td colspan="4" align="center" class="empty">
                    <div><i class="iconfont icon-zanwushuju"></i></div>
                    <div>暂无数据</div>
                </td>
            </tr>
        @endif
        </tbody>
    </table>

    <script src="/vendor/kt.logger/layui/layui.js"></script>
    <script src="/vendor/kt.logger/js/index.js"></script>
</body>
</html>
