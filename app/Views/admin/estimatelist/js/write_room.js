Vue.prototype.$common = app_lib;
Vue.prototype.$app_estimate = app_estimate;
let app = new Vue({
  el: '#app',
  data: {
    action_type: 'insert',
    room_action_type: 'insert',
    is_popup: view_data.is_popup, // 
    commoncode_1: view_data.commoncode_1, // 
    commoncode_2: view_data.commoncode_2, // 
    commoncode_3: view_data.commoncode_3, // 침대타입
    commoncode_4: view_data.commoncode_4, // 뷰타입
    commoncode_5: view_data.commoncode_5, // 건물구분
    commoncode_6: view_data.roomtype_list, // 객실타입
    commoncode_7: [], // 객실타입
    accom_list: view_data.accom_list, // 숙박업체 리스트
    is_discount: false,
    checkedRes: [],
    searched_rooms: {
      count_total: 0,
      is_available: 0,
    },
    assignable_rooms: [],
    is_room_searched: false,
    estimate: [],
    selectedRoom: {
      room_no: null,
      room_type_no: null,
      room_group: null,
      reservation_no: null,
      room_number: null,
      origin_room_no: null,
    },
    target_room_detail: [],
    rental_car_list: app_estimate.lib_rental_car_list,
    golf_club_cost_list: app_estimate.lib_golf_club_cost_list,
    activity_list: app_estimate.lib_activity_list,
    golf_cost_list: app_estimate.lib_golf_cost_list,
    exchange: app_estimate.lib_exchange,
    activity_exchange_rate: app_estimate.lib_activity_exchange_rate,
    car_exchange_rate: app_estimate.lib_car_exchange_rate,
    editorContent: '',
    is_loding_list_1: false,
    showRommChange: false,
    list: [],

    //검색관련 변수 
    search: {
      sel_exp_ci_date: view_data.cal_exp_ci_date,
      sel_exp_co_date: null,
      sel_room_type_no: null,  // 검색할 룸타입을 받아올것.
      //sel_floor:
      sel_rooms: 1,
      sel_nights: 1,
      sel_guests: 4,
      sel_accoms: view_data.accom_nos,
      sel_room_no: '',

    },
    //배정객실 검색관련 변수 
    room_search: {
      sel_exp_ci_date: null,
      sel_exp_co_date: null,
      sel_room_type_no: null,  // 검색할 룸타입을 받아올것.
      //sel_floor:
      sel_rooms: 1,
      sel_nights: 1,
      sel_guests: 4,
    },
    selectedImage: null,

  },
  watch: {

  },

  computed: {
    filteredRoomList() {
      if (!Array.isArray(this.commoncode_7.list)) {
        return [];
      }
      if (!this.search.sel_room_type_no) {
        return this.commoncode_7.list;
      }

      console.log("🚀 ~ filteredRoomList ~ this.search.sel_room_no:", this.search.sel_room_no)

      let filteredRoomList =  this.commoncode_7.list.filter(room => room.room_type_no === this.search.sel_room_type_no);
      console.log("🚀 ~ filteredRoomList ~ filteredRoomList:", filteredRoomList)
      //만약 필더된 룸 리스트에 선택된 방번호 sel_room_no 가 없다면 "" 빈값을 넣어준다. 
      if (filteredRoomList.length > 0 && !filteredRoomList.find(room => room.no === this.search.sel_room_no)) {
        this.search.sel_room_no = "";
      }
      return filteredRoomList;
      
    },
    groupedAssignableRooms() {
      const groups = {};
      if (this.assignable_rooms.room_list && this.assignable_rooms.room_list.length) {
        this.assignable_rooms.room_list.forEach(room => {
          if (!groups[room.floor_nm]) {
            groups[room.floor_nm] = [];
          }
          groups[room.floor_nm].push(room);
        });
      }
      return Object.values(groups);
    }
  },

  methods: {

    removeHtmlTags(str) {
      if (str) {
        const doc = new DOMParser().parseFromString(str, 'text/html');
        return doc.body.textContent || doc.body.innerText || "";
      }
      return str;
    },
    setDateRange() {
      var start = new Date(this.search.sel_exp_ci_date);
      var end = new Date(this.search.sel_exp_co_date);
      var dateArray = [];

      while (start <= end) {
        dateArray.push(new Date(start));
        start.setDate(start.getDate() + 1);
      }

      this.dateRange = dateArray;
    },
    openPopupVue(url) {
      // Calculate the position of the window to be centered on the screen
      let width = 1000;
      let height = 1000;  // Set the height to the height of the screen


      let left = (window.screen.width - width) / 2;
      let top = (window.screen.height - height) / 2;
      let target_url = index_url + url;
      // Open the popup window with the specified URL, width, height, and position
      window.open(target_url, '_blank', `width=${width}, height=${height}, left=${left}, top=${top}, resizable=no`);
    },

    // 견적상태가 확정으로 변경시, 객실 예약이 가능한지 확인한다.
    changes_estimate_status() {

      // do nothing at the moment...

      //check the changed status
      //console.log('estimate status changed to:', app_estimate.lib_estimate.status);
      //비동기 저장
      //app_lib._save('/api/estimate/changes_estimate_status', app_estimate.lib_estimate, this.action_type, 'stay');

    },
    assign_selectRoom(assignable_rooms_item) {

      this.selectedRoom.room_no = assignable_rooms_item.no;
      this.selectedRoom.room_type_no = assignable_rooms_item.room_type_no;
      this.selectedRoom.room_number = assignable_rooms_item.room_number;
    },
    async open_assign_room_modal(reservation_no = 0, room_group = 0, room_no = null) {

      /*   if (reservation_no == 0) {
          toastr.error(app_lib.toastr_project, '예약번호를 선택해주세요.');
          return false;
        } */
      if (room_group == 0) {
        toastr.error(app_lib.toastr_project, '객실그룹을 선택해주세요.');
        return false;
      }
      this.selectedRoom.room_group = room_group;
      this.selectedRoom.reservation_no = reservation_no;
      this.selectedRoom.room_no = room_no;
      this.selectedRoom.origin_room_no = room_no;
      await this.get_assignable_roomlist();
      // Open the modal
      //$('#assign_room_modal').modal('show');
      this.showRommChange = true;
      console.log('this.assignable_rooms', this.assignable_rooms);
    },
    set_change_assign_room() {
      this.target_room_detail = app_estimate.lib_estimate.room_estimate.room_checked[0].detail_rooms;

      let target_indices = this.target_room_detail
        .map((x, i) => x.room_group == this.selectedRoom.room_group && x.room_type_no == this.selectedRoom.room_type_no ? i : -1)
        .filter(i => i !== -1);

      target_indices.forEach(index => {
        this.target_room_detail[index].room_no = this.selectedRoom.room_no;
        this.target_room_detail[index].room_number = this.selectedRoom.room_number;
      });

      // If there are no errors, emit an event to close the modal
      if (!this.hasErrors) {
        this.showRommChange = false;
        //$('.modal').modal('hide');
      }
    },
    //배정가능 객실 조회 
    async get_assignable_roomlist() {

      let params = new FormData();

      //this.is_loding_list_1 = true;
      //estimate.room_estimate.res_info estimate.room_estimate.res_info
      this.room_search.sel_room_type_no = app_estimate.lib_estimate.room_estimate.res_info.room_type_no;
      this.room_search.sel_exp_ci_date = app_estimate.lib_estimate.room_estimate.res_info.exp_ci_date;
      this.room_search.sel_exp_co_date = app_estimate.lib_estimate.room_estimate.res_info.exp_co_date;
      this.room_search.sel_rooms = app_estimate.lib_estimate.room_estimate.res_info.rooms;


      params.append("postdata", JSON.stringify(this.room_search));

      await axios
        .post('/api/estimate/get_assignable_roomlist', params)
        .then(response => {
          console.info('axios response :', response.data.result);
          if (response.data.status == 200) {
            this.assignable_rooms = response.data.result.list;
            //this.checkedRes = [];
          } else {
            toastr.error(app_lib.toastr_project, response.data.message);
          }
        })
        .catch(error => {
          console.info('error', 'API호출에 실패했습니다.' + error)
        })
        .finally(() => {
          this.is_loding_list_1 = false;

        });
    },
    get_room_types_accom() {
      //accom_list 에서 accom_nos 로 검색하여 room_types 를 가져온다.
      let targetItem = this.accom_list.find(item => item.no == view_data.accom_nos);
      //console.log("🚀 ~ get_room_types_accom ~ targetItem:", targetItem)

      if (targetItem) {
        return targetItem.room_type_nos;
      } else {
        return 0;
      }

    },
    //예약 가능한 객실 조회 - 자동선택
    async getavailableroom_autoselect() {

      let params = new FormData();

      this.is_loding_list_1 = true;
      //check mem_is_host_group
      if (view_data.mem_is_host_group == 1 && view_data.mem_is_partner_group == 0) {
        if (this.search.sel_room_type_no == '') {
          this.search.sel_room_type_no = this.get_room_types_accom();
          console.log("🚀 ~ getavailableroom_autoselect ~  this.search.sel_room_type_no:", this.search.sel_room_type_no)
          if (this.search.sel_room_type_no == 0) {
            toastr.error('PMS', '객실타입을 찾을 수 없습니다.');
            this.is_loding_list_1 = false;
            return false;
          }
        }
      } else {
        this.search.sel_room_type_no = app_estimate.get_reservation_room_types();

      }
      params.append("postdata", JSON.stringify(this.search));

      await axios
        .post('/api/estimate/getavailableroom', params)
        .then(response => {
          console.info('getavailableroom_autoselect response :', response.data.result);
          if (response.data.status == 200) {
            this.searched_rooms = response.data.result.list;
            this.checkedRes = [];
            if (this.searched_rooms.count_total > 0) {
              //this.searched_rooms.result_ab_room_type 을 반복하여 , is_available 이 1인것을 찾아서 모두 selected_room 에 담는다.
              let selected_room = [];
              this.searched_rooms.result_ab_room_type.forEach(element => {
                if (element.is_available == 1 && element.is_room_assignable == 1) {
                  selected_room.push(element);
                } else {
                  //console.log("🚀 ~ getavailableroom_autoselect ~ element:", element)
                }
              });

              if (selected_room) {
                this.searched_rooms.count_total = selected_room.length;
                this.searched_rooms.result_ab_room_type = selected_room;
              } else {
                this.searched_rooms.count_total = 0;
              }

            }
            if (this.searched_rooms.count_total > 0) {
              console.log("🚀 ~ getavailableroom_autoselect ~ this.searched_rooms:", this.searched_rooms)

              /* this.searched_rooms.result_ab_room_type.forEach(element => {
                //search.sel_room_no 와 같은것을 찾아서 checkedRes 에 추가한다.
                //없을경우  this.is_available 0 , this.is_room_assignable 0, searched_rooms.count_total = 0 으로 변경한다.
                if (element.room_no == this.search.sel_room_no) {
                  this.checkedRes.push(element.room_type_no);
                  this.is_available = element.is_available;
                  this.is_room_assignable = element.is_room_assignable;
                } else {
                  this.is_available = 0;
                  this.is_room_assignable = 0;
                  this.searched_rooms.count_total = 0;
                }
              }); */

              // 자동선택
              this.is_available = this.searched_rooms.result_ab_room_type[0].is_available;
              this.is_room_assignable = this.searched_rooms.result_ab_room_type[0].is_room_assignable;
              this.checkedRes.push(this.searched_rooms.result_ab_room_type[0].room_type_no);
              app_estimate.selectRoom();
            }



          } else {
            toastr.error('PMS', response.data.message);
          }
        })
        .catch(error => {
          console.info('error', 'API호출에 실패했습니다.' + error)
        })
        .finally(() => {
          this.is_loding_list_1 = false;
          this.is_room_searched = true;
        });
    },

    //예약 가능한 객실 조회 
    async getavailableroom() {

      let params = new FormData();

      this.is_loding_list_1 = true;
      this.search.sel_room_type_no = app_estimate.get_reservation_room_types();
      params.append("postdata", JSON.stringify(this.search));

      await axios
        .post('/api/estimate/getavailableroom', params)
        .then(response => {
          console.info('axios response :', response.data.result);
          if (response.data.status == 200) {
            this.searched_rooms = response.data.result.list;
            this.checkedRes = [];
          } else {
            toastr.error('PMS', response.data.message);
          }
        })
        .catch(error => {
          console.info('error', 'API호출에 실패했습니다.' + error)
        })
        .finally(() => {
          this.is_loding_list_1 = false;
          this.is_room_searched = true;
        });
    },
    gp_to_list() {
      location.href = "/df-admin/estimate/reserlist";

    },
    //삭제
    async delete_estimate() {

      let params = new FormData();
      params_arr = this.detail;
      params.append("pkey", app_estimate.lib_estimate.no);
      this.is_loding_list_1 = true;

      let action_url = '';
      if (this.action_type == 'update') {
        action_url = '/api/estimate/delete_estimate'
      } else {
        console.info('MERIT', '삭제 할 수 없습니다.')
        return false;
      }

      await axios
        .post(action_url, params)
        .then(response => {
          console.info('axios response :', response.data);
          if (response.data.status == 200) {

            $('#modal_delete').modal('hide');
            toastr.success('MERIT', response.data.message);

            setTimeout(() => app_lib.back_to_list(), 1000);
          } else {
            toastr.error('MERIT', response.data.message);

          }
        })
        .catch(error => {
          toastr.error('MERIT', 'API호출에 실패했습니다.' + error);
          //console.info('error', 'API호출에 실패했습니다.' + error)
        })
        .finally(() => {
          this.is_loding_list_1 = false;
          //location.href = "./list";
        });
    },
    //견적마스터 생성
    async save_estimate() {

      //set customer's info
      app_estimate.lib_estimate.room_estimate.guest_name = app_estimate.lib_estimate.guest_name;
      app_estimate.lib_estimate.room_estimate.res_info.guest_name = app_estimate.lib_estimate.guest_name;
      app_estimate.lib_estimate.room_estimate.res_info.guests = app_estimate.lib_estimate.guests;
      app_estimate.lib_estimate.room_estimate.guest_hp = app_estimate.lib_estimate.guest_hp;
      app_estimate.lib_estimate.room_estimate.guest_email = app_estimate.lib_estimate.guest_email;
      app_estimate.lib_estimate.room_estimate.air_schedule = app_estimate.lib_estimate.air_schedule;
      app_estimate.lib_estimate.room_estimate.checkin_local = app_estimate.lib_estimate.checkin_local;
      app_estimate.lib_estimate.room_estimate.res_guest_class_cd = app_estimate.lib_estimate.res_guest_class_cd;
      //  app_estimate.lib_estimate.room_estimate.res_guest_class_cd = app_estimate.lib_estimate.res_guest_class_cd;
      delete_estimate_return = [];
      //app_estimate.lib_estimate
      console.log("🚀 ~ file: write.js:169 ~ save_estimate ~ app_estimate.lib_estimate:", app_estimate.lib_estimate)
      // return false;
      let params = new FormData();

      //전체 환율은 의미가 없어서, 나중에 제거 처리 대기
      app_estimate.lib_estimate.exchange = app_estimate.lib_exchange; // 저장시에는 지정된 환율을 반영한다.

      app_estimate.lib_estimate.type = 'estimate'; // estimate 구분

      params.append("postdata", JSON.stringify(app_estimate.lib_estimate));
      params.append("action_type", this.action_type);
      params.append("is_manager", 'Y'); // 관지라 직접 견적을 생성하는 경우 필수 입력값 조정을 위한 구분값

      app_lib.is_loding_list_1 = true;

      await axios
        .post('/api/estimate/save_estimate', params)
        .then(response => {
          console.info('axios response :', response.data);
          this.axios_response = response.data;
          // console.log("🚀 ~ file: write.js:186 ~ save_estimate ~ axios_response:", axios_response)
          if (response.data.status == 200) {

            $('.modal').modal('hide');
            toastr.success(app_lib.toastr_project, response.data.message);
            //setTimeout(() => location.href = '/df-admin/estimate/hostroom/write_host/' + response.data.result.estimate_no, 1000);
            //reload  page
            // setTimeout(() => loaction.reload(), 1000);
            if (this.is_popup) {
              setTimeout(() => {
                location.reload();
                if (window.opener) {
                  window.opener.location.reload();
                }
              }, 1000);
            }

          } else {
            toastr.error(app_lib.toastr_project, response.data.message);

          }
        })
        .catch(error => {
          toastr.error(app_lib.toastr_project, 'API호출에 실패했습니다.' + error);
        })
        .finally(() => {
          app_lib.is_loding_list_1 = false;
        });
    },

    // 화면 초기화 값 전달 2196f3
    async front_construct() {
      let params = new FormData();
    },

    initializeDatepicker(elementId, singleDate) {
      let today = new Date();

      var picker = new Lightpick({
        field: document.getElementById(elementId),
        format: 'YYYY-MM-DD',
        numberOfMonths: 2,
        singleDate,
        startDate: today,
        endDate: new Date(today.getTime() + 24 * 60 * 60 * 1000),
        footer: true,
        firstDay: 7, // 일요일부터 시작하도록 설정

        onSelect: function (start, end) {
          if (start && end) {
            var start = picker.getStartDate();
            var end = picker.getEndDate();
            //  this.search.sel_exp_ci_date = start.format('YYYY-MM-DD');
            //  this.search.sel_exp_co_date = end.format('YYYY-MM-DD');
            var diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            this.search.sel_nights = diff;
          }
          console.info('this.search.sel_nights', this.search.sel_nights)

        }.bind(this)

      });

      picker.setDateRange(today, new Date(today.getTime() + 24 * 60 * 60 * 1000) /* tomorrow */);


    },
  },
  updated() {
    this.$nextTick(() => {
      feather.replace();
    });
  },

  mounted: function () {
    this.estimate = app_estimate.lib_estimate;
    this.commoncode_7 = view_data.room_list; // 객실타입

    /* if ( view_data.data.is_temp == 1 && view_data.cal_room_date !== '') {
      console.log("🚀 ~ view_data.cal_room_date:", view_data.cal_room_date);
      this.search.sel_exp_ci_date = view_data.cal_room_date;
      // 필요한 경우에 따라 종료 날짜도 설정합니다.
      // 예를 들어, 종료 날짜가 시작 날짜로부터 1일 후라고 가정
      let startDate = new Date(view_data.cal_room_date);
      let endDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
      this.search.sel_exp_co_date = endDate.toISOString().split('T')[0];
      this.search.sel_nights = 1; // 예시로 1박 설정
      // Datepicker 초기화
      this.initializeDatepicker('exp_date', false); // for singleDate=true
      let picker = new Lightpick({
        field: document.getElementById('exp_date'),
        format: 'YYYY-MM-DD',
        numberOfMonths: 2,
        singleDate: false,
        startDate: startDate,
        endDate: endDate,
        footer: true,
        firstDay: 7,
        onSelect: function (start, end) {
          if (start && end) {
            var start = picker.getStartDate();
            var end = picker.getEndDate();
            this.search.sel_exp_ci_date = start.format('YYYY-MM-DD');
            this.search.sel_exp_co_date = end.format('YYYY-MM-DD');
            var diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            this.search.sel_nights = diff;
          }
          console.info('this.search.sel_nights', this.search.sel_nights);
        }.bind(this)
      });
      picker.setDateRange(startDate, endDate);
    } else {
      this.initializeDatepicker('exp_date', false); // for singleDate=true
  } */


    this.initializeDatepicker('exp_date', false); // for singleDate=true

    /**
     *  //검색관련 변수 
    search: {
      sel_exp_ci_date: view_data.cal_exp_ci_date,
      sel_exp_co_date: null,
      sel_room_type_no: null,  // 검색할 룸타입을 받아올것.
      //sel_floor:
      sel_rooms: 1,
      sel_nights: 1,
      sel_guests: 4,
      sel_accoms: view_data.accom_nos,

    },
     */

    if (view_data.cal_exp_ci_date !== '') {
      this.search.sel_exp_ci_date = view_data.cal_exp_ci_date;
      // set this.search.sel_exp_co_date 2days after this.search.sel_exp_ci_date
      let startDate = new Date(view_data.cal_exp_ci_date);
      let endDate = new Date(startDate.getTime() + 24 * 60 * 60 * 1000);
      this.search.sel_exp_co_date = endDate.toISOString().split('T')[0];
    }
    if (view_data.cal_room_type_no !== '' && view_data.cal_room_no !== 0) {
      this.search.sel_room_type_no = view_data.cal_room_type_no;
      this.search.sel_room_no = view_data.cal_room_no;
    }



  },
  created() {

    this.action_type = view_data.action_type;
    if (this.action_type == 'update') {
      app_estimate.lib_estimate = view_data.data;

      app_estimate.dateRange = app_estimate.getDatesArray(view_data.data.room_estimate.res_info.exp_ci_date, view_data.data.room_estimate.res_info.exp_co_date);

    } else {
      app_estimate.lib_estimate.mem_id = null;

    }
    this.front_construct()

  }
})

