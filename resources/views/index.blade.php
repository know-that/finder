<!DOCTYPE html>
<html>
<head>
    @foreach($configFinder['default_cdn']['css'] as $item)
        <link rel="stylesheet" href="{{ $item }}">
    @endforeach
    <link rel="stylesheet" href="{{ $configFinder['static_url'] }}/vendor/kt.finder/css/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">

    @foreach($configFinder['default_cdn']['js'] as $item)
    <script src="{{ $item  }}"></script>
    @endforeach
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
            :before-close="() => visible = false">
            <span>@{{ file.contents }}</span>
        </el-dialog>
    </div>

    <script src="{{ $configFinder['static_url'] }}/vendor/kt.finder/js/index.js"></script>
</body>
</html>
