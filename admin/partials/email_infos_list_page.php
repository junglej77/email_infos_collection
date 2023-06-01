<div id="app" class="jungle_email_infos_setup">
    <el-table ref="table" :data="tableData" row-key="email" @sort-change="sort_change" :highlight-current-row="true" stripe>
        <el-table-column v-for="(item, key) in columns" :key="key" :prop="key" :class-name="key" v-bind="item">
            <template #header>
                <div class="title_intro">
                    <el-tooltip v-if="columns[key].headerIntro" class="box-item" effect="dark" :content="columns[key].headerIntro" placement="top-start">
                        <span>
                            {{ columns[key].label }}
                            <svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" data-v-ea893728="">
                                <path fill="currentColor" d="M512 64a448 448 0 1 1 0 896 448 448 0 0 1 0-896zm0 192a58.432 58.432 0 0 0-58.24 63.744l23.36 256.384a35.072 35.072 0 0 0 69.76 0l23.296-256.384A58.432 58.432 0 0 0 512 256zm0 512a51.2 51.2 0 1 0 0-102.4 51.2 51.2 0 0 0 0 102.4z">
                                </path>
                            </svg>
                        </span>
                    </el-tooltip>
                    <span v-else>{{ columns[key].label }}</span>
                    <span v-if="columns[key].sortable" class="caret-wrapper">
                        <i class="sort-caret ascending"></i>
                        <i class="sort-caret descending"></i>
                    </span>
                </div>
                <div class="title_filter" v-if="columns[key].queryForm" @click.stop>
                    <!-- 搜索页面板块 -->
                    <el-input v-if="key == 'alias'" v-model.trim="queryForm.data.alias" placeholder="搜索板块" @change="getTableList" clearable></el-input>
                </div>
            </template>
            <template v-if="key == 'oerationSelf'" #default="scope">
                <el-button type="primary" text @click="handleAddPop(scope.row)">新增</el-button>
                <el-button type="warning" text @click="handleEditPop(scope.row)">编辑</el-button>
            </template>
        </el-table-column>
    </el-table>
</div>
<script>
    const app = Vue.createApp({
        data() {
            return {
                queryForm: {
                    order: 'ASC',
                    orderby: "menu_order",
                    data: {},
                },
                columns: {
                    name: {
                        label: "名称",
                    },
                    email: {
                        label: "邮箱",
                    },
                    phone: {
                        label: "手机",
                    },
                    ip_address: {
                        label: "ip",
                    },
                    country: {
                        label: "国家",
                    },
                    countryCode: {
                        label: "国家代码",
                    },
                    state: {
                        label: "州/省",
                    },
                    city: {
                        label: "城市",
                    },
                    device: {
                        label: "设备",
                    },
                    browser: {
                        label: "浏览器",
                    },
                    send_time: {
                        label: "发送时间",
                    },
                    send_count: {
                        label: "发送次数",
                    },
                    status: {
                        label: "状态",
                    },
                    remark: {
                        label: "备注",
                    },
                    oerationSelf: {
                        label: "操作",
                        width: 158,
                        fixed: "right",
                    },
                },
                tableData: []
            }
        },
        mounted() {

            this.$nextTick(() => {
                this.getList()
            })
        },
        methods: {
            sort_change(column) {
                Object.assign(this.queryForm, {
                    order: column.order == "ascending" ? 'ASC' : 'DESC',
                    orderby: column.prop,
                });
                this.getList();
            }, // 排序
            getList() {
                axios.post('/wp-json/get/infos/list', {}).then(Response => {
                    this.tableData = Response.data
                }).catch(e => {
                    console.log(e);
                });
            },
            clone(obj) {
                var o;
                // 如果  他是对象object的话  , 因为null,object,array  也是'object';
                if (typeof obj === 'object') {

                    // 如果  他是空的话
                    if (obj === null) {
                        o = null;
                    } else {

                        // 如果  他是数组arr的话
                        if (obj instanceof Array) {
                            o = [];
                            for (var i = 0, len = obj.length; i < len; i++) {
                                o.push(this.clone(obj[i]));
                            }
                        }
                        // 如果  他是对象object的话
                        else {
                            o = {};
                            for (var j in obj) {
                                o[j] = this.clone(obj[j]);
                            }
                        }

                    }
                } else {
                    o = obj;
                }
                return o;
            }
        }
    });
    app.use(ElementPlus).mount('#app')
</script>