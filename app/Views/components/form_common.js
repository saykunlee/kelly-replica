const formCommon = {
    methods: {
        // 조회 (GET)
        fetchDetails(url, id, successCallback, idKey = 'no') {
            console.log(`Fetching details for ID: ${id}`);
            let params = new FormData();
            params.append(idKey, id);
            axios.post(url, params)
                .then(response => {
                    console.log("🚀 ~ fetchDetails ~ response.data:", response.data);
                    if (response.data.status === '200') {
                        successCallback(response.data.data);

                    } else {
                        toastr.error(response.data.message);
                        console.error('Details not found');
                    }
                })
                .catch(error => {
                    toastr.error('Error fetching details.');
                    console.error('Error fetching details:', error);
                });
        },

        // 등록/수정/삭제 (POST)
        postData(url, params, modal_close = true) {
            return axios.post(url, params)
                .then(response => {
                    if (response.data.status === '200') {
                        toastr.success(response.data.message || '요청이 성공적으로 처리되었습니다.');
                        //모든 모달 닫기
                        if (modal_close) {
                            $('.modal').modal('hide');
                        }
                        return response.data;
                    } else if (response.data.status === '422') {
                        response.data.messages.forEach(err => {
                            toastr.error(err.message, `유효성 검사 오류`);
                        });

                    } else {
                        toastr.error(response.data.message);

                    }
                })
                .catch(error => {
                    toastr.error('예기치 않은 오류가 발생했습니다.');
                    console.error('Error:', error);
                });
        },

        createFormData(details) {
            let params = new FormData();
            for (let key in details) {
                if (details.hasOwnProperty(key) && details[key] !== null) {
                    if (Array.isArray(details[key])) {
                        details[key].forEach(value => {
                            if (value !== null) {
                                params.append(key + '[]', value);
                            }
                        });
                    } else {
                        params.append(key, details[key]);
                    }
                }
            }
            return params;
        },



        /**
         * validateAndSave 함수
         * 
         * 이 함수는 주어진 데이터를 검증하고, 검증이 통과되면 서버에 데이터를 전송합니다. 
         * 폼 검증을 건너뛰거나, 폼 ID를 통해 폼을 검증할 수 있습니다. 
         * 검증 후, 데이터는 수정 모드에 따라 지정된 URL로 전송됩니다.
         * 
         * @param {Object} options - 함수에 전달되는 옵션 객체
         * @param {string|null} options.formId - 검증할 폼의 ID. 'skip'이면 검증을 건너뜁니다.
         * @param {string} options.updateUrl - 수정 모드일 때 데이터를 전송할 URL
         * @param {string} options.createUrl - 생성 모드일 때 데이터를 전송할 URL
         * @param {Object} options.dataObject - 서버에 전송할 데이터 객체
         * @param {boolean} [options.isEditMode=false] - 수정 모드인지 여부
         * @param {Function} [options.callback=() => {}] - 서버 응답 후 실행할 콜백 함수
         * 
         * @example
         * validateAndSave({
         *     formId: 'myForm',
         *     updateUrl: '/api/update',
         *     createUrl: '/api/create',
         *     dataObject: { name: 'John', age: 30 },
         *     isEditMode: true,
         *     callback: (response) => { console.log(response); }
         * });
         * 
         * @returns {Promise} - 서버 응답을 포함한 Promise 객체
         * 
         * 서버 응답 데이터 형태:
         * {
         *     "status": "200", // 또는 다른 상태 코드
         *     "message": "성공 메시지 또는 오류 메시지",
         *     "data": { ... } // 서버에서 반환한 데이터 객체
         * }
         * 
         * - `status`: 응답의 상태를 나타내며, 일반적으로 '200', 'success', 'error' 등의 값을 가집니다.
         * - `message`: 응답에 대한 설명 메시지를 포함합니다.
         * - `data`: 서버에서 반환한 실제 데이터를 포함하며, 이는 생성된 레코드의 ID나 상세 정보일 수 있습니다.
         */
        validateAndSave({
            formId = null,  // 폼 ID가 지정되지 않으면 null로 설정
            updateUrl,
            createUrl,
            dataObject,
            isEditMode = false,  // 기본값은 생성 모드
            callback = () => { }  // 콜백 함수, 기본적으로 빈 함수
        }) {
            // URL 결정: 수정 모드에 따라 updateUrl 또는 createUrl 선택
            const url = isEditMode ? updateUrl : createUrl;
            const params = this.createFormData(dataObject); // FormData 생성

            // 폼 검증 여부에 따라 데이터 전송
            const sendData = () => {
                this.postData(url, params)
                    .then((response) => {
                        // 성공 시 callback 실행
                        console.log("🚀 ~ validateAndSave ~ response:", response);
                        if (typeof callback === 'function') {
                            callback(response);
                        }
                    })
                    .catch((error) => {
                        console.error('Error during save process:', error);
                    });
            };

            // 폼 ID가 'skip'인 경우 폼 검증을 건너뜁니다.
            if (formId === 'skip') {
                sendData();
                return; // 함수 종료
            }

            // 폼을 찾고 검증
            const form = document.getElementById(formId) || document.querySelector('form');
            if (!form) {
                console.error('폼을 찾을 수 없습니다.');
                return;
            }

            console.log("🚀 ~ form:", form);

            // 폼 검증 수행
            const parsleyForm = $(form).parsley();
            if (parsleyForm.validate()) {
                sendData();
            }
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
        back_to_list(url) {

            const newUrl = new URL(url, window.location.origin);
            newUrl.searchParams.set('b', '1');
            window.location.href = newUrl.toString();

        },
        uploadImage(event, dataObject, upload_dir) {
            //console.log("🚀 ~ file: images.js:52 ~ uploadImage ~ event,pkey,upload_dir:", event, pkey, upload_dir)
            let file = event.target.files[0];
            let fieldName = event.target.name; // Get the field name from the input tag

            // Append the field name to the FormData
            let formData = new FormData();
            formData.append("pkey", dataObject.mem_id);
            dataObject.no = dataObject.mem_id;
            formData.append("postdata", JSON.stringify(dataObject)); // no 가 들어있는 vue 변수
            formData.append("image", file);
            formData.append("target_column", fieldName); // Append the field name to the FormData
            formData.append("upload_dir", upload_dir); // Append the field name to the FormData
            formData.append("model", 'Member_model');
            console.log("🚀 ~ uploadImage ~ fieldName:", fieldName)
            // Call the API
            axios
                .post('/api/member-api/upload-image', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    if (response.data.status === '200') {
                        //if the response.data.message is null, use successMessage
                        if (response.data.message === null) {
                            toastr.success(successMessage);
                        } else {
                            toastr.success(response.data.message);
                        }
                        //fieldName에 해당되는 
                        this.detail[fieldName] = response.data.data.upload_result.data.filename;
                        //reload icon
                        this.$nextTick(() => {
                            feather.replace();
                        });

                    } else if (response.data.status === '422') {
                        // 유효성 검사 오류 처리
                        response.data.messages.forEach(err => {
                            toastr.error(err.message, `유효성 검사 오류`);
                        });
                        // 모달을 닫지 않음
                    } else {
                        toastr.error(response.data.message);

                    }
                })
                .catch(error => {
                    toastr.error('예기치 않은 오류가 발생했습니다.');


                });
        },

        selectImage(index) {
            // console.log("🚀 ~ selectImage ~  this.detail[imageKey]:",  this.detail[imageKey])
            let imageKey = index;
            let imageSource = this.detail[imageKey] ? `/uploads/${this.detail[imageKey]}` : 'https://via.placeholder.com/640x426';
            let imageAlt = this.detail[`${imageKey}_filename`] || '';
            this.selectedImage = { source: imageSource, alt: imageAlt };
            ///console.log("🚀 ~ selectImage ~ this.selectedImage:", this.selectedImage)
            $('#imageModal').modal('show'); // Show the modal
        },
        deleteMemberImage(index) {
            this.detail.target_column = index;
            formCommon.methods.validateAndSave({
                formId: 'memberForm',
                updateUrl: '/api/member-api/delete-image',
                dataObject: this.detail,
                isEditMode: this.isEditMode,
                callback: (response) => {
                    this.detail[index] = null;
                    this.detail[index + '_filename'] = null;
                }
            });
        },


    },
};