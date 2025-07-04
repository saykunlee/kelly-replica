const modalCommon = {
    methods: {
        createFormData(details, formData = new FormData(), parentKey = '') {
            for (let key in details) {
                if (details.hasOwnProperty(key)) {
                    const value = details[key];
                    const formKey = parentKey ? `${parentKey}[${key}]` : key;

                    if (value !== null) {
                        if (Array.isArray(value)) {
                            value.forEach((item, index) => {
                                // 하위 배열 처리
                                this.createFormData(item, formData, `${formKey}[${index}]`);
                            });
                        } else if (typeof value === 'object') {
                            // 객체 처리
                            this.createFormData(value, formData, formKey);
                        } else {
                            formData.append(formKey, value);
                        }
                    }
                }
            }
            return formData;
        },
        handleAxiosRequest(url, params, successMessage, pkey_name = 'no') {
            axios.post(url, params)
                .then(response => {
                    if (response.data.status === '200') {
                        //if the response.data.message is null, use successMessage
                        if (response.data.message === null) {
                            toastr.success(successMessage);
                        } else {
                            toastr.success(response.data.message);
                        }
                        this.reloadDataTable(pkey_name);
                        $('#modal_save').modal('hide'); // 모달 닫기
                    } else if (response.data.status === '422') {
                        // 유효성 검사 오류 처리
                        response.data.messages.forEach(err => {
                            toastr.error(err.message, `유효성 검사 오류`);
                        });
                        // 모달을 닫지 않음
                    } else {
                        toastr.error(response.data.message);
                        $('#modal_save').modal('hide'); // 모달 닫기
                    }
                })
                .catch(error => {
                    toastr.error('예기치 않은 오류가 발생했습니다.');
                    //$('#modal_save').modal('hide'); // 모달 닫기
                    return Promise.resolve();
                });
        },
        reloadDataTable(pkey_name) {
            let table = $('#list').DataTable();
            let currentPage = table.page.info().page;
            let selectedRowId = this.selectedNo;

            table.ajax.reload(function () {
                table.page(currentPage).draw(false);
                table.rows().every(function () {
                    if (String(this.data()[pkey_name]) === String(selectedRowId)) {
                        $(this.node()).addClass('selected-row');
                    }
                });

                setTimeout(function () {
                    let rowNode = table.rows().nodes().to$().filter('.selected-row');
                    if (rowNode.length) {
                        rowNode[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }, 500);
            }, false);
        },
        fetchDetails(url, id, successCallback, idKey = 'no') {
            console.log(`Fetching details for ID: ${id}`);
            let params = new FormData();
            params.append(idKey, id);
            axios.post(url, params)
                .then(response => {
                    if (response.data.status === '200') {
                        successCallback(response.data.data);
                        this.selectedNo = id;
                        $('#modal_save').modal('show');
                    } else {
                        toastr.error(response.data.message);
                        console.error('Details not found');
                    }
                })
                .catch(error => {
                    toastr.error('Error fetching details.');
                    console.error('Error fetching details:', error);
                });
        }
    }
};
