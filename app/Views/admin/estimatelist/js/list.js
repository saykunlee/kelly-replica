Vue.prototype.$common = app_lib;

var app = new Vue({
    el: '#app',
    data: {
        action_type: 'insert',
        commoncode_1: view_data.commoncode_1, // 
        commoncode_2: view_data.commoncode_2, // 
        commoncode_3: view_data.commoncode_3, // 
        buttons_array: ['pageLength', 'excel'],
        save_action_url: '',
        detail: [],
        total_rows: 0,
        list: [],

        selected: {
            life_factor_no: 0,
            startDate: '',
            endDate: '',
            title: '',
            life_code: '',
            factor_unit_type: '',
            factor_class_nm: '',
            practice_unit_nm: '',
            factor_unit_nm: '',
            factor_jugi_nm: '',
            day_count: '',
            unit: '',
            showUnitInputDateM: false,
            showUnitType1: false,
            bigo_1: '',
        },

        search: {
            type: "",
            status: "",
            type_name: null,
            accom_type: "",
            show_yn: "",
            bigo_1: null,
            useing: 1,
            insert_date: null,
            insert_id: null,
            update_date: null,
            update_id: null,
            is_deleted: 0,
            sdate: "",
            edate: "",
        },

        checkedRes: [],
    },
    computed: {

    },
    methods: {
        // 견적상태가 확정으로 변경시, 객실 예약이 가능한지 확인한다.
        async changes_estimate_status(item) {
            console.log("🚀 ~ changes_estimate_status ~ item:", item)

            console.log('estimate status changed to:', item.estimate_status);
            item.exp_ci_date = item.room.list[0].exp_ci_date;
            item.exp_co_date = item.room.list[0].exp_co_date;
            item.room_type_no = item.room.list[0].room_type_no;
            item.rooms = item.room.list[0].rooms;
            item.nights = item.room.list[0].nights;
            //비동기 저장
            await app_lib._save('/api/estimate/changes_estimate_status_reslist', item, this.action_type, 'stay');
            console.info('result_delete_room', app_lib.axios_response);
            if (app_lib.axios_response.status !== "200") {
                
                item.estimate_status = item.estimate_status_origin;
            }else {
                item.estimate_status_origin =  item.estimate_status;
            }
            $('#list').DataTable().destroy();
            app_lib.ini_dt('list', 0, this.buttons_array);
        },
        filterColumn(_dtid, i, s_text) {
            app_lib.filterColumn(_dtid, i, s_text)
        },
        filterColumn_range(_dtid, i, s_date, e_date) {
            app_lib.filterColumn_range(_dtid, i, s_date, e_date)

        },
        go_detail(_dtid, no = 0, row = 0) {
            app_lib.go_detail(_dtid, no, row);

        },
        go_invoice(_dtid, no = 0, row = 0) {
            app_lib.go_detail(_dtid, no, row);

        },
        select_list(idx) {
            app.list[idx].unit = 1;
            app.selected = app.list[idx];
        },
        // 기록
        async achive_merit() {
            //app_lib._save(this.save_action_url, this.selected, this.action_type, 'stay');
        },
        get_new_list() {
            // DataTables 인스턴스 제거
            $('#list').DataTable().destroy();
            // list 배열 초기화
            //this.list = [];

            //async get_list_param(_api_url, postdata = [], _dtid, df_useing = 0, buttons = ['pageLength', 'excel'])
            app_lib.get_list_param('/api/estimate/get_estimate_list', this.search, 'list', 0, this.buttons_array);



        },
        
    },

    mounted: function () {

    },

    created() {
        let today = new Date();
        let year = today.getFullYear();
        let month = today.getMonth() + 1; // JavaScript의 getMonth()는 0부터 시작하기 때문에 1을 더해줍니다.
        let day = today.getDate(); // 오늘 날짜를 가져옵니다.

        // 월과 일이 한 자리수인 경우 앞에 0을 붙여줍니다.
        let monthString = month < 10 ? `0${month}` : `${month}`;
        let dayString = day < 10 ? `0${day}` : `${day}`;

        this.search.edate = `${year}-${monthString}-${dayString}`; // 오늘 날짜로 설정

        let oneMonthAgo = new Date();
        oneMonthAgo.setMonth(today.getMonth() - 1); // 한 달 전으로 설정
        year = oneMonthAgo.getFullYear();
        month = oneMonthAgo.getMonth() + 1;
        day = oneMonthAgo.getDate();

        // 월과 일이 한 자리수인 경우 앞에 0을 붙여줍니다.
        monthString = month < 10 ? `0${month}` : `${month}`;
        dayString = day < 10 ? `0${day}` : `${day}`;

        this.search.sdate = `${year}-${monthString}-${dayString}`; // 한 달 전의 날짜로 설정



        this.get_new_list();
    }
})
