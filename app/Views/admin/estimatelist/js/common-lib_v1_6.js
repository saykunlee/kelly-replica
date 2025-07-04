app_lib = new Vue({

    data: {
        toastr_project: 'AutoInvoice', // toastr message title.
        is_loding_list_1: false,
        is_loding_list_2: false,
        is_loding_list_3: false,
        axios_result: false,
        axios_response: {},
        //  session_mem_id : 	session_mem_id,
        response: {}, //call back response data 
        ut_datatable: {
            selected_row: view_data.selected_row,
            selected_page: view_data.selected_page,
            len_page: view_data.len_page,
        },
    },
    mounted: {

    },
    methods: {
        resetObj(estimateObj, arr = []) {
            //console.log("🚀 ~ file: common-lib_v1_6.js:21 ~ resetObj ~ estimateObj, arr :", estimateObj, arr)

            const keys = Object.keys(estimateObj);
            keys.forEach(key => {
                if (Array.isArray(estimateObj[key])) {
                    estimateObj[key] = [];
                } else {
                    estimateObj[key] = null;
                }
            });

            // 배열 파라미터가 전달된 경우
            if (Array.isArray(arr)) {
                arr.forEach(item => {
                    if (estimateObj.hasOwnProperty(item)) {
                        if (Array.isArray(estimateObj[item])) {
                            estimateObj[item] = [];
                        } else {
                            estimateObj[item] = null;
                        }
                    }
                });
            }
        },

        //date -> yyyy-mm-dd
        formattedDate(_date) {
            const _year = _date.getFullYear();
            const _month = String(_date.getMonth() + 1).padStart(2, '0');
            const _day = String(_date.getDate()).padStart(2, '0');
            return `${_year}-${_month}-${_day}`;

        },
        //yyyy-mm-dd -> yyyy년 mm월 dd일
         formattedDate_kor(_date) {
            const [year, month, day] = _date.split('-');
            return `${year}년 ${month}월 ${day}일`;
        },
        
        formatDateTime(datetime) {
            const date = new Date(datetime);
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based, so add 1 and pad with 0s
            const dd = String(date.getDate()).padStart(2, '0');
            const hh = String(date.getHours()).padStart(2, '0');
            const min = String(date.getMinutes()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd} ${hh}:${min}`;
        },
        formatMoney(number) {
            //console.log("🚀 ~ file: common-lib_v1_6.js:54 ~ formatMoney ~ number:", number)
            if (number == null) number = 0;
            // 3자리마다 쉼표를 추가하는 정규식을 사용합니다.
            const formatted = number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return `${formatted}`;
        },

        // 01010041004 -> 010-1004-1004
        formatPhone(phone) {

            if (phone == null) phone = '';
            //check if phone is 11 digits
            if (phone.length === 11) {
                var formatted = phone.replace(/(\d{3})(\d{4})(\d{4})/, '$1-$2-$3');
            } else if (phone.length === 10) {
                var formatted = phone.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
            } else {
                var formatted = phone;
            }

            return formatted;
        },


        //go back
        back_to_list() {
            let selected_info = index_url + "?page=" + this.ut_datatable.selected_page + "&row=" + this.ut_datatable.selected_row + "&len_page=" + this.ut_datatable.len_page;
            location.href = selected_info;
        },
        //개별 컬럼 필터  //리스트에서 검색 필터링 함수 
        filterColumn(_dtid, i, s_text) {
            utDt = $('#' + _dtid).DataTable();
            utDt
                .columns(i)
                .search(s_text)
                .draw();
        },

        // 여러 컬럼에서 텍스트를 필터링하는 함수
        filterColumns(_dtid, columnIndices, s_text) {
            const utDt = $('#' + _dtid).DataTable();

            // s_text가 유효한 문자열인지 확인합니다.
            if (!s_text || typeof s_text !== 'string') {
                s_text = '';  // s_text가 유효하지 않으면 빈 문자열로 설정합니다.
            }

            const regexStr = s_text.split(' ').join('|');  // 공백을 기준으로 단어를 분리하고, OR 조건으로 단어를 결합합니다.
            const regex = new RegExp(regexStr, 'i');  // 대소문자를 구분하지 않는 정규 표현식을 생성합니다.

            // custom search function
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    for (let i = 0; i < columnIndices.length; i++) {
                        const colIndex = columnIndices[i];
                        if (regex.test(data[colIndex])) {
                            return true;  // 해당 행이 검색 조건과 일치하면 true를 반환합니다.
                        }
                    }
                    return false;  // 해당 행이 검색 조건과 일치하지 않으면 false를 반환합니다.
                }
            );

            utDt.draw();  // 테이블을 다시 그립니다.

            // custom search function을 제거합니다.
            $.fn.dataTable.ext.search.pop();
        },

        // Existing function with a slight modification to use regex
        filterColumn_or(_dtid, i, s_text) {
            const utDt = $('#' + _dtid).DataTable();
            utDt
                .columns(i)
                .search(s_text, true, false)  // true enables regular expression
                .draw();
        },
        filterColumn_or_v2(_dtid, i, s_text) {
            const utDt = $('#' + _dtid).DataTable();
            const regexPattern = `^${s_text}$`;  // 정규식 수정
            utDt
                .columns(i)
                .search(regexPattern, true, false)  // true는 정규식을 활성화
                .draw();
        },
        //날짜 범위 필터
        filterColumn_range(_dtid, i, s_date, e_date) {
            console.log("filterColumn s_date", s_date);
            console.log("filterColumn e_date", e_date);
            const utDt = $('#' + _dtid).DataTable();
            const startDate = new Date(s_date);
            const endDate = new Date(e_date);

            utDt
                .rows()
                .every(function (rowIdx, tableLoop, rowLoop) {
                    var columnDate = new Date(this.cell(rowIdx, i).data());
                    if (columnDate >= startDate && columnDate <= endDate) {
                        $(this.node()).show();
                    } else {
                        $(this.node()).hide();
                    }
                });

            utDt.draw();
        },
        go_detail(_dtid, no = 0, row = 0, detail_url = 'write') {
            utDt = $('#' + _dtid).DataTable();
            this.ut_datatable.selected_page = utDt.page.info().page;
            action_url = index_url + "/" + detail_url + "/" + no;
            selected_info = "?page=" + this.ut_datatable.selected_page + "&row=" + row + "&len_page=" + utDt.page.len();
            location.href = action_url + selected_info;
        },
        //삭제
        async _delete_one(action_url, detail, action_type, callback_type = 'back_to_list') {

            let params = new FormData();
            params_arr = detail;
            params.append("pkey", detail.no);
            this.is_loding_list_1 = true;

            if (!action_type == 'update' || detail.no == 0) {
                // console.info(this.toastr_project, '삭제 할 수 없습니다.')
                toastr.error(this.toastr_project, '삭제 할 수 없습니다.');
                return false;
            }
            this.axios_result = false;
            await axios
                .post(action_url, params)
                .then(response => {
                    console.info('axios response :', response.data);
                    this.axios_response = response.data;
                    if (response.data.status == 200) {
                        $('.modal').modal('hide');
                        toastr.success(this.toastr_project, response.data.message);
                        if (callback_type == 'stay') {
                            // -- stay
                        } else if (callback_type == 'stay_with_response') {
                            this.response = response.data;
                            //console.info('stay_with_response:', this.response );
                        } else if (callback_type == 'reload') {
                            setTimeout(() => location.reload(), 1000);
                        } else if (callback_type == 'return') {
                            this.axios_result = true;
                        } else {
                            setTimeout(() => this.back_to_list(), 1000);
                        }
                    } else {
                        toastr.error(this.toastr_project, response.data.message);
                    }
                })
                .catch(error => {
                    toastr.error(this.toastr_project, 'API호출에 실패했습니다.' + error);
                    //console.info('error', 'API호출에 실패했습니다.' + error)
                })
                .finally(() => {
                    this.is_loding_list_1 = false;
                    //location.href = "./list";
                });
        },

        //저장
        async _save(action_url, detail, action_type, callback_type = 'back_to_list', loding = 1) {
            console.log("🚀 ~ file: common-lib_v1_6.js:155 ~ _save ~ detail:", detail)

            let params = new FormData();
            params.append("postdata", JSON.stringify(detail));
            params.append("action_type", action_type);
            params.append("pkey", detail.no);

            if (loding == 1) {
                this.is_loding_list_1 = true;
            } else if (loding == 2) {
                this.is_loding_list_2 = true;
            }

            await axios
                .post(action_url, params)
                .then(response => {
                    console.info('axios response :', response.data);
                    this.axios_response = response.data;
                    if (response.data.status == 200) {

                        $('.modal').modal('hide');
                        toastr.success(this.toastr_project, response.data.message);

                        if (callback_type == 'stay') {
                            this.response = response.data;
                            // -- stay
                        } else if (callback_type == 'stay_with_response') {
                            this.response = response.data;
                            //   console.info('stay_with_response:', this.response );
                        } else if (callback_type == 'reload') {
                            setTimeout(() => location.reload(), 1000);
                        } else if (callback_type == 'return') {
                            this.response = response.data;
                            this.axios_result = true;
                        } else {
                            setTimeout(() => this.back_to_list(), 1000);
                        }
                    } else {
                        this.response = response.data;
                        toastr.error(this.toastr_project, response.data.message);

                    }
                })
                .catch(error => {
                    toastr.error(this.toastr_project, 'API호출에 실패했습니다.' + error);
                })
                .finally(() => {
                    this.is_loding_list_1 = false;
                    this.is_loding_list_2 = false;
                });
        },

        async get_list(_api_url, _dtid, df_useing = 0, buttons = ['pageLength', 'excel']) {
            let params = new FormData();
            this.is_loding_list_1 = true;
            await axios
                .post(_api_url, params)
                .then(response => {
                    console.log("get_list res", response)
                    this.response = response.data;
                    if (response.status == 200) {
                        app.list = response.data.result.list;
                        app.total_rows = response.data.result.total_rows;

                    } else {
                        //console.info('error', 'API호출에 실패했습니다.' + response.data.message)
                        toastr.error(this.toastr_project, response.data.message);
                    }
                })
                .catch(error => {
                    toastr.error(this.toastr_project, 'API호출에 실패했습니다.' + error);
                })
                .finally(() => {
                    this.is_loding_list_1 = false;
                });
            this.ini_dt(_dtid, df_useing, buttons);

        },
        async get_list_param(_api_url, postdata = [], _dtid, df_useing = 0, buttons = ['pageLength', 'excel']) {

            let params = new FormData();
            params.append("postdata", JSON.stringify(postdata));
            this.is_loding_list_1 = true;
            await axios
                .post(_api_url, params)
                .then(response => {
                    console.log("get_list res", response)
                    this.response = response.data;
                    if (response.status == 200) {
                        if (response.data.result.list == 'undefined' || response.data.result.list == null) {

                            app.list = response.data.result;
                            app.total_rows = response.data.result.length;
                        } else {
                            app.list = response.data.result.list;
                            app.total_rows = response.data.result.total_rows;

                        }

                    } else {
                        //console.info('error', 'API호출에 실패했습니다.' + response.data.message)
                        toastr.error(this.toastr_project, response.data.message);
                    }
                })
                .catch(error => {
                    toastr.error(this.toastr_project, 'API호출에 실패했습니다.' + error);
                })
                .finally(() => {
                    this.is_loding_list_1 = false;
                });
            this.ini_dt(_dtid, df_useing, buttons);

        },
        ini_dt(_dtid, df_useing = 0, buttons = ['pageLength', 'excel']) {
            let dtId = "#" + _dtid;
            let utDt;

            if ($.fn.DataTable.isDataTable(dtId)) {
                $(dtId).DataTable().clear().destroy();
            }

            utDt = $(dtId).DataTable({
                dom: 'Bfrtip',
                searching: true,
                rowId: 'extn',
                buttons: buttons,
                aaSorting: [],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All'],
                ],
            });

            // Apply any additional logic or configurations

            // Filter column if required
            if (df_useing !== 0) {
                this.filterColumn(_dtid, df_useing, app.search.useing);
            }

            // Update page length if necessary
            console.log("🚀 ~ file: common-lib_v1_6.js:222 ~ ini_dt ~ this.ut_datatable.len_page:", this.ut_datatable.len_page)
            if (this.ut_datatable.len_page > 10 || this.ut_datatable.len_page == -1) {
                utDt.page.len(this.ut_datatable.len_page).draw();

            }

            // Go to selected page
            if (this.ut_datatable.selected_page > 0) {
                utDt.page(Number(this.ut_datatable.selected_page)).draw('page');
            }

            // Highlight selected row
            if (this.ut_datatable.selected_row > 0) {
                utDt.rows(Number(this.ut_datatable.selected_row)).nodes().to$().addClass('selected');
            }
            feather.replace();
        },
        //순수하게 api 결과를 반환하는 함수 
        // _api_url : call url 
        // _return : return value 레퍼런스 형식
        async call_restapi(_api_url, params, _return) {
            this.is_loding_list_1 = true;
            await axios
                .post(_api_url, params)
                .then(response => {
                    console.log("call_restapi res", response)
                    this.axios_response = response.data;
                    if (response.status == 200) {
                        //    return response.data.result.list;
                        this.response = response;
                        this.$set(app, _return, response.data.result); // Dynamically assign the retrieved value to the specified variable
                    } else {
                        //console.info('error', 'API호출에 실패했습니다.' + response.data.message)
                        toastr.error(this.toastr_project, response.data.message);
                    }
                })
                .catch(error => {
                    toastr.error(this.toastr_project, 'API호출에 실패했습니다.' + error);
                })
                .finally(() => {
                    this.is_loding_list_1 = false;
                });

        },

        //순수하게 api 결과를 반환하는 함수 
        // _api_url : call url 
        // _return : return value 레퍼런스 형식
        // param 형식 추가  
        async call_restapi_param(_api_url, postdata = [], _return, loding = 1) {
            let params = new FormData();
            params.append("postdata", JSON.stringify(postdata));
            if (loding == 1) {
                this.is_loding_list_1 = true;
            } else if (loding == 2) {
                this.is_loding_list_2 = true;
            }
            await axios
                .post(_api_url, params)
                .then(response => {
                    console.log("call_restapi response", response)
                    this.axios_response = response.data;
                    if (response.status == 200) {
                        //    return response.data.result.list;
                        this.$set(app, _return, response.data.result); // Dynamically assign the retrieved value to the specified variable
                        this.response = response;

                    } else {
                        //console.info('error', 'API호출에 실패했습니다.' + response.data.message)
                        toastr.error(this.toastr_project, response.data.message);

                    }
                })
                .catch(error => {
                    toastr.error(this.toastr_project, 'API호출에 실패했습니다.' + error);
                })
                .finally(() => {
                    this.is_loding_list_1 = false;
                    this.is_loding_list_2 = false;
                });

        },


        // 검색하고자하는 json과 항목명 값을 넣으면 필터링해서 반환해준다.
        remapData(jsonData, key, searchValue) {
            const remappedData = jsonData.filter(obj => obj[key] === searchValue);
            console.info("remapData", remappedData);
            return remappedData;
        },

        // 배열을 받아서 , 로 구분된 문자열로 반환한다.
        array_to_string(arr) {
            //배열이 아니거나 길이가 0인 경우 빈 문자열을 반환한다.
            if (!Array.isArray(arr) || arr.length === 0) {
                return "";
            } else {
                return arr.join(",");
            }
        },
        // , 로 구분된 문자열을 받아서 배열로 반환한다.
        string_to_array(str) {
            //문자열이 아니거나 길이가 0인 경우 빈 배열을 반환한다.
            if (typeof str !== "string" || str.length === 0) {
                return [];
            } else {
                return str.split(",");
            }
        },

        isValidEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        },



    },

    created() {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };


    }
})
