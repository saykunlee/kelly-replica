<div id="app_datatable_settings">
     <?php if ($_SESSION['mem_is_developer'] == 1) : ?>
         <hr class="mg-t-20">
 
    <fieldset class="form-fieldset">

        <legend>Datatable Settings</legend>
        <input type="hidden" name="id" v-model="datatableData.datatableSettings.id">
        <input type="hidden" name="menu_id" v-model="datatableData.datatableSettings.menu_id">
        

        <div class="row">
            <div class="form-group col-md-6">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" v-model="datatableData.datatableSettings.name" required>
            </div>

            <div class="form-group col-md-6">
                <label for="url">URL</label>
                <input type="text" class="form-control" id="url" v-model="datatableData.datatableSettings.url" required>
            </div>

            <div class="form-group col-md-6">
                <label for="buttons">Buttons</label>
                <input type="text" class="form-control" id="buttons" v-model="datatableData.datatableSettings.dt_buttons" required>
            </div>

            <div class="form-group col-md-2">
                <label for="showCheckbox">Show Checkbox</label>
                <select class="form-control" id="showCheckbox" v-model="datatableData.datatableSettings.showCheckbox">
                    <option value="1">Show</option>
                    <option value="0">Hide</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label for="pageLength">pageLength</label>
                <select class="form-control" id="pageLength" v-model="datatableData.datatableSettings.pageLength">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div class="form-group col-md-2">
                <label for="is_active">상태</label>
                <select class="form-control" id="is_active" v-model="datatableData.datatableSettings.is_active">
                    <option value="1">활성</option>
                    <option value="0">비활성</option>
                </select>
            </div>

            <div class="form-group col-md-12">
                <hr class="mg-t-20">
                <button class="btn btn-primary mg-r-10" @click="saveSettings">저장하기</button>
                <button class="btn btn-danger" @click="deleteSettings">삭제하기</button>
            </div>
        </div>
    </fieldset>
    <template v-if="datatableData.datatableSettings.id">

        <fieldset class="form-fieldset mg-t-40">
            <legend>Datatable Columns</legend>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-bordered table-sm ">
                        <tbody>
                            <tr v-for="(column, index) in datatableData.datatableColumns" :key="index">
                                <td class="bg-gray-100 tx-center">{{ index + 1 }}</td>
                                <td class="wd-100-p">
                                    <div class="form-row pd-t-10 pd-d-10">

                                        <div class="form-group col-md-2">
                                            <label :for="'name_' + index">바인딩</label>
                                            <select class="form-control" :id="'name_' + index" v-model="column.data" required>
                                                <option v-for="col in bindColumns" :value="col">{{ col }}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label :for="'title_' + index">제목</label>
                                            <input type="text" class="form-control" :id="'title_' + index" v-model="column.title" required>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label :for="'width_' + index">너비</label>
                                            <input type="text" class="form-control" :id="'width_' + index" v-model="column.width">
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label :for="'className_' + index">CSS</label>
                                            <input type="text" class="form-control" :id="'className_' + index" v-model="column.className">
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label :for="'order_' + index">순서</label>
                                            <input type="number" class="form-control" :id="'order_' + index" v-model="column.order">
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label :for="'render_' + index">렌더링</label>
                                            <textarea class="form-control" :id="'render_' + index" v-model="column.render" rows="3"></textarea>
                                        </div>

                                        <div class="form-group custom-control custom-checkbox col-md-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" :id="'orderable_' + index" :checked="column.orderable == 1" @change="toggleOrderable(index)">
                                                <label class="custom-control-label" :for="'orderable_' + index">정렬사용</label>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" :id="'visible_' + index" :checked="column.visible == 1" @change="toggleVisible(index)">
                                                <label class="custom-control-label" :for="'visible_' + index">노출</label>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                                <td class="wd-80-f align-middle tx-center">
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-primary" @click="saveColumn(index)">저장</button>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-danger" @click="removeColumn(index)">삭제</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-primary" @click="addColumn">Add Column</button>
                </div>
            </div>
        </fieldset>
    </template>
    <?php endif; ?>
</div>

<script>
    const datatableSettingsApp = Vue.createApp({
        data() {
            return {
                app_name: 'datatableSettingsApp',
                menu_id: <?= json_encode($currentMenu['no']) ?>, // 데이터베이스에서 가져온 데이터
                datatableData: <?= json_encode($datatableData) ?>, // 데이터베이스에서 가져온 데이터
                bindColumns: [] // Ensure bindColumns is defined
            };
        },
        methods: {
            saveSettings() {
                console.log('설정 저장 버튼 클릭됨');

                let params = new FormData();
                for (let key in this.datatableData.datatableSettings) {
                    if (this.datatableData.datatableSettings.hasOwnProperty(key)) {
                        // Conditionally append the 'id' field
                        if (key !== 'id' || this.datatableData.datatableSettings.id) {
                            params.append(key, this.datatableData.datatableSettings[key]);
                        }
                    }
                }            
                axios.post('/api/datatable-settings-api/save-settings', params)
                    .then(response => {
                        console.log('API response:', response.data);
                        if (response.data.status === '200') {
                            toastr.success('설정이 성공적으로 업데이트되었습니다.');
                            // Assign the returned id to the datatableData.datatableSettings.id if it's a new record
                            if (response.data.id) {
                                this.datatableData.datatableSettings.id = response.data.id;
                            }
                        } else {
                            toastr.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating settings:', error);
                    });
            },
            deleteSettings() {
                if (!this.datatableData.datatableSettings.id) {
                    toastr.error('삭제할 설정이 없습니다.');
                    return;
                }
                let params = new FormData();
                params.append('id', this.datatableData.datatableSettings.id);

                if (confirm('정말로 이 설정을 삭제하시겠습니까?')) {
                    axios.post('/api/datatable-settings-api/delete-settings', params)
                        .then(response => {
                            console.log('API response:', response.data);
                            if (response.data.status === '200') {
                                toastr.success('설정이 성공적으로 삭제되었습니다.');
                                // Clear the datatable settings data
                                this.datatableData.datatableSettings = {};
                                this.datatableData.datatableColumns = [];
                            } else {
                                toastr.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting settings:', error);
                            toastr.error('설정 삭제 중 오류가 발생했습니다.');
                        });
                }
            },
            addColumn() {
                this.datatableData.datatableColumns.push({
                    setting_id: this.datatableData.datatableSettings.id,
                    data: '',
                    title: '',
                    width: '100px',
                    className: 'tx-center',
                    orderable: 0,
                    render: '',
                    visible: 1,
                    order: null
                });
            },
            removeColumn(index) {
                const columnId = this.datatableData.datatableColumns[index].id;

                let params = new FormData();
                params.append('id', columnId);
                // Make an API call to delete the column from the database
                axios.post('/api/datatable-settings-api/delete-column', params)
                    .then(response => {
                        if (response.data.status === '200') {
                            // Remove the column from the local data
                            this.datatableData.datatableColumns.splice(index, 1);
                            toastr.success('컬럼이 성공적으로 삭제되었습니다.');
                        } else {
                            toastr.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting column:', error);
                        toastr.error('컬럼 삭제 중 오류가 발생했습니다.');
                    });
            },
            saveColumn(index) {
                console.log('컬럼 저장 버튼 클릭됨', index);

                let column = this.datatableData.datatableColumns[index];
                let params = new FormData();
                for (let key in column) {
                    if (column.hasOwnProperty(key)) {
                        params.append(key, column[key]);
                    }
                }

                axios.post('/api/datatable-settings-api/save-column', params)
                    .then(response => {
                        console.log('API response:', response.data);
                        if (response.data.status === '200') {
                            toastr.success('컬럼이 성공적으로 업데이트되었습니다.');
                            // Assign the returned id to the column if it's a new record
                            if (response.data.id) {
                                this.datatableData.datatableColumns[index].id = response.data.id;
                            }
                        } else {
                            toastr.error(response.data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating column:', error);
                    });
            },
            toggleVisible(index) {
                this.datatableData.datatableColumns[index].visible = this.datatableData.datatableColumns[index].visible == 1 ? 0 : 1;
            },
            toggleOrderable(index) {
                this.datatableData.datatableColumns[index].orderable = this.datatableData.datatableColumns[index].orderable == 1 ? 0 : 1;
            }
        },
        mounted() {
            // Set default value for dt_buttons if not already set
            if (!this.datatableData.datatableSettings.dt_buttons) {
                this.datatableData.datatableSettings.dt_buttons = 'pageLength,excelHtml5,pdfHtml5,print';
            }
            // set default value for url 
            if (!this.datatableData.datatableSettings.url) {
                this.datatableData.datatableSettings.url = '/api/menu-api/';
            }
            // set default value for menu_id
            if (!this.datatableData.datatableSettings.menu_id) {
                this.datatableData.datatableSettings.menu_id = this.menu_id;
            }
            //set pageLength
            if (!this.datatableData.datatableSettings.pageLength) {
                this.datatableData.datatableSettings.pageLength = 10;
            }
            // set default value for is_active
            if (!this.datatableData.datatableSettings.is_active) {
                this.datatableData.datatableSettings.is_active = 1;
            }
            
        }
    }).mount('#app_datatable_settings');
</script>
