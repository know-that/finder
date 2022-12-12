<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="/vendor/kt.finder/css/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>
<body>
    <div id="app">
        <el-row class="body" :gutter="20">
            <el-col :span="4">
                <el-tree
                    :props="props"
                    :load="loadNode"
                    icon-class="none"
                    class="tree"
                    lazy>
                        <div class="tree-content" slot-scope="{ node, data }" @click="getFile(data)">
                            <i v-if="data.type === 'dir'" class="el-icon-folder"></i>
                            <i v-else class="el-icon-document"></i>
                            <span :title="data.name">@{{ data.name }}</span>
                        </div>
                </el-tree>
            </el-col>
            <el-col :span="20">
                <div v-if="loading">
                    <div class="loading">
                        <div><i class="el-icon-loading"></i></div>
                        <div>加载中...</div>
                    </div>
                </div>

                <div v-else>
                    <div v-if="file.name">
                        <div style="margin-bottom: 10px">
                            <h2>
                                @{{ file.path }}
                                <el-button type="text" v-if="file.contents && file?.content_items?.length > 0" @click="visible = true">查看原文</el-button>
                            </h2>
                        </div>

                        <el-table
                            v-if="file?.content_items?.length > 0"
                            class="content"
                            :data="file.content_items"
                            border>
                            <el-table-column prop="date" label="日期" width="180"></el-table-column>
                            <el-table-column prop="type" label="环境" width="100"></el-table-column>
                            <el-table-column prop="level" label="级别" width="100">
                                <template slot-scope="scope">
                                    <el-tag v-if="['emergency', 'alert', 'critical', 'error'].indexOf(scope.row.level) >= 0" type="danger">@{{ scope.row.level }}</el-tag>
                                    <el-tag v-else-if="['warning', 'notice'].indexOf(scope.row.level) >= 0" type="warning">@{{ scope.row.level }}</el-tag>
                                    <el-tag v-else type="info">@{{ scope.row.level }}</el-tag>
                                </template>
                            </el-table-column>
                            <el-table-column prop="content" label="内容">
                                <template slot-scope="scope">@{{ scope.row.content  }}</template>
                            </el-table-column>
                        </el-table>
                        <div v-else class="view">@{{ file.contents }}</div>
                    </div>
                    <el-empty v-else :description="error.message || '请在左侧选择需要查看的文件'"></el-empty>
                </div>
            </el-col>
        </el-row>

        <el-dialog
            width="50%"
            :title="file.path"
            :visible.sync="visible"
            :modal-append-to-body="false"
            :close-on-click-modal="false"
            :before-close="() => visible = false">
            <span>@{{ file.contents }}</span>
        </el-dialog>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="/vendor/kt.finder/js/index.js"></script>
</body>
</html>

{{--<!doctype html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <meta charset="utf-8">--}}
{{--    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">--}}
{{--    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">--}}
{{--    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>--}}
{{--    <link rel="stylesheet" href="/vendor/kt.finder/iconfont/iconfont.css">--}}
{{--</head>--}}
{{--<body>--}}
{{--    <div id="app">--}}
{{--        <v-btn--}}
{{--            elevation="2"--}}
{{--            plain--}}
{{--        >321132</v-btn>--}}
{{--    </div>--}}
{{--    <div class="layui-row">--}}
{{--        <div class="layui-col-md3">--}}
{{--            <div id="catalogues"></div>--}}
{{--        </div>--}}

{{--        <div class="layui-col-md9">--}}
{{--            <div class="layui-card">--}}
{{--                <div class="layui-card-header">卡片面板</div>--}}
{{--                <div class="layui-card-body">--}}
{{--                    <table class="layui-table">--}}
{{--                        <thead>--}}
{{--                        <tr>--}}
{{--                            <th lay-data="{width:200}">名称</th>--}}
{{--                            <th>类型</th>--}}
{{--                            <th>大小</th>--}}
{{--                            <th>权限</th>--}}
{{--                            <th>修改日期</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}

{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <script src="/vendor/kt.finder/layui/layui.js"></script>--}}
{{--    <script src="/vendor/kt.finder/iconfont/iconfont.js"></script>--}}
{{--    <script src="/vendor/kt.finder/js/index.js"></script>--}}
{{--</body>--}}
{{--</html>--}}
