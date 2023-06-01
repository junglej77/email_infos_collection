<div id="app" class="jungle_email_infos_setup">
    <p>只需要按钮添加ADDITIONAL CSS CLASS(ES) --- "get-quote" 即可点击弹出对应收集客户反馈信息的弹窗</p>
    <p>以下是你希望发送邮箱的配置：</p>
    <p>发件人名称=> <input type="text" v-model="jungle_email_name"></p>
    <p>邮箱账号=> <input type="text" v-model="jungle_email_account"></p>
    <p>密码=> <input type="text" v-model="jungle_email_password"></p>
    <p>发送邮箱服务器=> <input type="text" v-model="jungle_email_host"></p>
    <p>加密类型=> <input type="text" v-model="jungle_email_encryption"></p>
    <p>端口=> <input type="text" v-model="jungle_email_port"></p>
    <p>邮件回复内容=> <textarea id="jungle_email_auto_repaly" type="textarea" rows="4" placeholder="你想自动回复客户的话" v-model="jungle_email_auto_repaly"></textarea></p>
    <button @click="submit">提交</button>
</div>
<script>
    const app = Vue.createApp({
        data() {
            return {
                jungle_email_host: '',
                jungle_email_encryption: '',
                jungle_email_port: '',
                jungle_email_account: '',
                jungle_email_name: '',
                jungle_email_password: '',
                jungle_email_auto_repaly: ''
            }
        },
        mounted() {
            this.getEmailSetup()
        },
        methods: {
            getWpOption: async (name) => {
                try {
                    const response = await axios.get(`/wp-json/myplugin/v1/option/${name}`);
                    return response.data;
                } catch (error) {
                    console.error(error);
                }
            },
            async updateAddWpOption(name, value) {
                let flag = await this.getWpOption(name)
                try {
                    const response = await axios.post(`/wp-json/myplugin/v1/${flag?'option':'addOption'}`, {
                        name: name,
                        value: value
                    });
                    return response.data;
                } catch (error) {
                    console.error(error);
                }
            },
            async getEmailSetup() {
                this.jungle_email_host = await this.getWpOption('jungle_email_host') || ''
                this.jungle_email_encryption = await this.getWpOption('jungle_email_encryption') || ''
                this.jungle_email_port = await this.getWpOption('jungle_email_port') || ''
                this.jungle_email_account = await this.getWpOption('jungle_email_account') || ''
                this.jungle_email_name = await this.getWpOption('jungle_email_name') || ''
                this.jungle_email_password = await this.getWpOption('jungle_email_password') || ''
                this.jungle_email_auto_repaly = await this.getWpOption('jungle_email_auto_repaly') || ''
            },
            submit() {
                this.updateAddWpOption('jungle_email_host', this.jungle_email_host);
                this.updateAddWpOption('jungle_email_encryption', this.jungle_email_encryption);
                this.updateAddWpOption('jungle_email_port', this.jungle_email_port);
                this.updateAddWpOption('jungle_email_account', this.jungle_email_account);
                this.updateAddWpOption('jungle_email_name', this.jungle_email_name);
                this.updateAddWpOption('jungle_email_password', this.jungle_email_password);
                this.updateAddWpOption('jungle_email_auto_repaly', this.jungle_email_auto_repaly);
                this.$message({
                    type: 'success',
                    message: '已更新',
                    offset: 200
                })
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