<div id="app_datatable">
    <!-- DataTable -->
    <div class="row">
        <div class="col-lg-12">

            <div style="overflow-x:auto; padding-bottom: 15px;">
                <table id="list" class="table cell-border hover datatable-list" style="table-layout: fixed" width="1131px">
                    <thead>
                        <tr>
                            <th v-for="column in datatableData.datatableColumns" :key="column.data">
                                {{ column.title }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables가 데이터를 여기에 바인딩합니다 -->
                        <tr id="no_table_settings">
                            <td :colspan="datatableData.datatableColumns.length" class="text-center">설정 데이터가 없습니다.</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-white">
                            <td :colspan="datatableData.datatableColumns.length" class="mg-15-f">
                                <div id="placeholder" class="placeholder-paragraph pd-15">
                                    <div class="line"></div>
                                    <div class="line"></div>
                                    <div class="line"></div>
                                    <div class="line"></div>
                                    <div class="line"></div>
                                    <div class="line"></div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    const datatableApp = Vue.createApp({
        data() {
            return {
                app_name: 'datatableApp',
                datatableData: <?= json_encode($datatableData) ?>, // 데이터베이스에서 가져온 데이터
                selectedItems: [], // Array to store selected items
                startPage: null, // 현재 페이지
                pageLength: null, // 페이지당 항목 수
            };
        },
        methods: {
            getAjaxParams(dt_search, d) {

                let params = new FormData();

                // search 객체의 모든 항목을 FormData에 추가
                for (let key in dt_search) {
                    if (dt_search.hasOwnProperty(key)) {
                        if (Array.isArray(dt_search[key])) {
                            dt_search[key].forEach(value => {
                                params.append(key + '[]', value);
                            });
                        } else {
                            params.append(key, dt_search[key]);
                        }
                    }
                }

                // DataTables가 서버로 전송하는 기본 매개변수 추가
                for (let key in d) {
                    if (d.hasOwnProperty(key)) {
                        params.append(key, d[key]);
                    }
                }
                return params;
            },

            ini_dt(_dtid, dt_search) {
                let dtId = "#" + _dtid;
                let utDt;

                // 모든 설정 값이 존재하는지 확인
                const settings = this.datatableData.datatableSettings;

                if (!settings || !settings.pageLength || !settings.dt_buttons || !settings.url || settings.is_active !== "1") {
                    // console.error('Datatable settings are incomplete, missing, or inactive.');
                    $(dtId + ' tbody').show();
                    $(dtId + ' tfoot').hide();
                    $('#no_table_settings').show();
                    return;
                }
                // settings.pageLength
                if (this.pageLength !== null) {
                    settings.pageLength = this.pageLength;
                }
                if ($.fn.DataTable.isDataTable(dtId)) {
                    $(dtId).DataTable().clear().destroy();
                }

                // 테이블 요소가 존재하는지 확인
                if ($(dtId).length) {

                    utDt = $(dtId).DataTable({
                        dom: 'Bfrtip',
                        searching: true,
                        rowId: 'no', // 행의 ID를 'no' 필드로 설정
                        aaSorting: [],
                        serverSide: true, // 서버 사이드 처리 활성화
                        processing: false, // 처리 중 표시 비활성화
                        pageLength: settings.pageLength, // 기본 25개씩 출력
                        displayStart: this.startPage !== null ? (this.startPage - 1) * (this.pageLength !== null ? this.pageLength : settings.pageLength) : 0, // currentPage 설정
                        buttons: settings.dt_buttons.split(','), // datatableSettings의 dt_buttons 값 사용
                        // ini_dt 메서드 내 DataTable 설정에서 추가
                        pagingType: 'full_numbers', // 'full_numbers'로 설정하면 '처음', '이전', '다음', '마지막으로' 버튼 모두 활성화

                        lengthMenu: [
                            [10, 25, 50, 100, -1],
                            [10, 25, 50, 100, 'All'],
                        ],

                        ajax: {
                            url: settings.url, // datatableSettings의 url 값 사용
                            type: 'POST',
                            data: function(d) {
                                // Include search parameters
                                d.search = dt_search; // Include search parameters
                                return JSON.stringify(d); // JSON 문자열로 변환하여 전송
                            }.bind(this), // Vue 인스턴스의 this를 바인딩
                            contentType: 'application/json', // JSON 형식으로 전송
                            processData: false,
                            dataSrc: function(response) {
                                console.log("🚀 ~ ini_dt ~ response:", response)
                                // 데이터가 예상치 못한 형식일 경우를 대비한 오류 처리
                                if (!response.data) {
                                    alert('Invalid data format: ' + response.message);
                                    console.error('Invalid data format:', response);
                                    return [];
                                }
                                datatableSettingsApp.bindColumns = response.columns; // Ensure bindColumns is assigned
                                return response.data; // 'data' 키를 사용하여 데이터 반환
                            },
                            error: function(xhr, status, error) {
                                console.error('API 호출 중 오류 발생:', status, error); // 오류 발생 시 콘솔에 출력
                                // 에러 메시지를 테이블에 표시
                                $(dtId + ' tbody').html(
                                    '<tr class="text-center"><td colspan="10">데이터를 불러오는 중 오류가 발생했습니다: ' + error + '</td></tr>'
                                );
                            }
                        },

                        ...settings, // 전달된 설정 적용
                        columns: [
                            ...this.datatableData.datatableColumns.map(col => ({
                                ...col,
                                orderable: col.orderable === "1" ? true : false, // orderable 값 변환
                                visible: col.visible === "1" ? true : false, // visible 값 변환
                                render: col.render ? new Function('return ' + col.render)() : null
                            }))
                        ],
                        language: {
                            searchPlaceholder: '검색...',
                            sSearch: '',
                            lengthMenu: '페이지당 _MENU_ 항목 보기',
                            info: "총 _TOTAL_ 개 항목 중 _START_ 부터 _END_ 까지 표시",
                            infoFiltered: "(전체 _MAX_ 건 중 검색결과)",
                            paginate: {
                                first: '처음',
                                last: '마지막',
                                next: '다음',
                                previous: '이전'
                            },
                            buttons: {
                                pageLength: {
                                    _: '%d 개씩 보기',
                                    '-1': '전체 보기'
                                }
                            }
                        },
                        headerCallback: function(thead, data, start, end, display) {
                            $(thead).find('th').addClass('tx-center-f');
                        },
                        createdRow: function(row, data, dataIndex) {
                            $(row).find('td').addClass('valign-middle-f ');
                        },
                        rowCallback: function(row, data) {
                            // 필요시 추가

                        }.bind(this),
                        drawCallback: function(settings) {

                            this.startPage = settings._iDisplayStart / settings._iDisplayLength + 1;
                            this.pageLength = settings._iDisplayLength;

                        }.bind(this)
                    });

                    // 행 클릭 이벤트 리스너 추가
                    $(dtId + ' tbody').on('click', 'tr', function() {
                        let rowData = utDt.row(this).data();
                        //   utDt.$('tr.selected-row').removeClass('selected-row');
                        //  $(this).addClass('selected-row');
                    });

                    // "select all" 체크박스 이벤트 리스너 추가
                    $('#select-all').on('click', (event) => {
                        const rows = utDt.rows({
                            'search': 'applied'
                        }).nodes();
                        const isChecked = event.target.checked;
                        $('input[type="checkbox"]', rows).prop('checked', isChecked);

                        // selectedItems 배열 업데이트
                        if (isChecked) {
                            this.selectedItems = utDt.rows({
                                'search': 'applied'
                            }).data().toArray().map(row => parseInt(row.mem_id, 10));
                            $(rows).addClass('selected-row'); // Add selected-row class to all rows
                        } else {
                            this.selectedItems = [];
                            $(rows).removeClass('selected-row'); // Remove selected-row class from all rows
                        }
                    });

                    // 개별 행 체크박스 이벤트 리스너 추가
                    $(dtId + ' tbody').on('change', 'input.row-select', (event) => {
                        const rowId = parseInt($(event.target).data('row-id'), 10);
                        const row = $(event.target).closest('tr');
                        if (event.target.checked) {
                            // 중복 체크 방지
                            if (!this.selectedItems.includes(rowId)) {
                                this.selectedItems = [...this.selectedItems, rowId];
                            }
                            row.addClass('selected-row'); // Add selected-row class to the row
                        } else {
                            // 체크 해제된 값을 삭제
                            this.selectedItems = this.selectedItems.filter(item => item !== rowId);
                            row.removeClass('selected-row'); // Remove selected-row class from the row
                        }
                    });

                    // DataTables 이벤트 리스너 추가
                    $(dtId).on('processing.dt', function(e, settings, processing) {
                        if (processing) {
                            $(dtId + ' tbody').hide();
                            $(dtId + ' tfoot').show();
                            $('#no_table_settings').hide();
                        } else {
                            $('#no_table_settings').show();
                            $(dtId + ' tbody').show();
                            $(dtId + ' tfoot').hide();
                            // feather.replace() 호출 전에 아이콘이 유효한지 확인
                            try {
                                feather.replace();
                            } catch (error) {
                                console.error('Feather Icons replace error:', error);
                            }
                        }
                    });

                } else {
                    console.error('Table element not found: ' + dtId);
                }

            },
            go_url(url = '/') {
                //set sessionStorage
                sessionStorage.setItem('search', JSON.stringify(mainApp.search));
                sessionStorage.setItem('startPage', this.startPage);
                //pageLength
                sessionStorage.setItem('pageLength', this.pageLength);
                location.href = url;
            },
            go_detail(no = 0, row = 0, detail_url = '/') {
                //set sessionStorage
                sessionStorage.setItem('search', JSON.stringify(mainApp.search));
                sessionStorage.setItem('startPage', this.startPage);
                //pageLength
                sessionStorage.setItem('pageLength', this.pageLength);
                location.href = detail_url + "/" + no;
            },
        },
        watch: {
            selectedItems(newVal) {
                console.log("🚀 ~ selectedItems:", newVal);
            }
        },
        mounted() {
            //datatable 
            //this.ini_dt('list', {}, null); // 빈 객체를 기본 검색 파라미터로 전달
        }
    }).mount('#app_datatable');
</script>