	<style>
		.estimate_date {
			min-width: 95%;

		}
	</style>

	<!-- managermtype -->
	<div id="app">
		<!-- search area -->
		<hr class="mg-b-20">

		<div class="search_area">
			<div class="row">

				<div class="form-group col-lg-3">
					<label for="formGroupExampleInput" class="d-block">견적일자</label>
					<!-- <input type="date" class="form-control estimate_date" placeholder="시작 선택" @change="filterColumn_range('list',12,search.sdate,search.edate)" v-model="search.sdate"> -->
					<input type="date" class="form-control estimate_date" placeholder="시작 선택" v-model="search.sdate">
				</div>

				<div class="form-group col-lg-3">
					<label for="formGroupExampleInput" class="d-block">&nbsp;</label>
					<!-- <input type="date" class="form-control estimate_date" placeholder="시작 선택" @change="filterColumn_range('list',12,search.sdate,search.edate)" v-model="search.edate"> -->
					<input type="date" class="form-control estimate_date" placeholder="시작 선택" v-model="search.edate">
				</div>
				<div class="form-group col-lg-3">
					<label for="formGroupExampleInput2" class="d-block">견적고객명</label>
					<input type="search" class="form-control" placeholder="견적고객명 검색" @keydown="filterColumn('list',13,search.guest_name)" v-model="search.guest_name">
				</div>

				<div class="form-group col-lg-2">
					<label for="formGroupExampleInput" class="d-block">상태</label>

					<select class="custom-select" @change="filterColumn('list',8,search.status)" v-model="search.status">

						<option value="">전체</option>
						<option value="RR">대기</option>
						<option value="CR">확정</option>
						<option value="RC">취소</option>
						<option value="RD">삭제</option>
						<option value="NS">노쇼</option>

					</select>
				</div>
				<div class="form-group col-lg-1">
					<label for="formGroupExampleInput" class="d-block">&nbsp;</label>

					<a class="btn  btn-primary" @click="get_new_list()" href="javascript:void(0);"> <i data-feather="search"></i> </a>
				</div>

			</div>
		</div>

		<!-- DataTable -->
		<div v-if="$common.is_loding_list_1" class="row">
			<div class="col-lg-12">
				<div class="placeholder-paragraph mg-d-30">
					<div class="line"></div>
					<div class="line"></div>
					<div class="line"></div>
					<div class="line "></div>
					<div class="line"></div>
					<div class="line"></div>
					<div class="line"></div>
					<div class="line"></div>
				</div>

			</div>
		</div>
		<div v-else class="row">
			<div class="col-lg-12">
				<!-- <hr class="mg-b-30"> -->
				<!-- 
			[
			{
				"no": 32,
				"bs_platform": 0,
				"bs_platform_nm": null,
				"customer_no": null,
				"customer_name": null,
				"staff_no": null,
				"staff_name": null,
				"status": "RR",
				"status_text": "대기",
				"guest_name": "자동견적이름",
				"guest_ename": "",
				"guest_hp": "010-119-119",
				"guest_phone": "",
				"guest_email": "auto_invoice@ai.com",
				"guest_address": "",
				"guest_password": "",
				"guest_requirement": "",
				"remark_1": "",
				"remark_2": "",
				"remark_3": "",
				"res_source_cd": null,
				"res_guest_market_cd": null,
				"res_guest_class_cd": null,
				"res_rsvn_cd": null,
				"is_open": 1,
				"is_open_text": "공개",
				"checkin_local": 0,
				"checkin_local_text": "무",
				"air_schedule": "",
				"room_amount": 5000,
				"rental_car_amount": 13000,
				"golf_amount": 12000,
				"extra_amount": 0,
				"dc_amount": 0,
				"resfee": 5000,
				"resfee_kr": 125000,
				"net_amount": 0,
				"net_amount_kr": 0,
				"vat_amount": 0,
				"vat_amount_kr": null,
				"total_amount": 30000,
				"total_amount_kr": 0,
				"exchange": null,
				"payment_method": "bank",
				"useing": 1,
				"insert_date": "2023-06-29 17:41:15",
				"insert_id": 1,
				"update_date": null,
				"update_id": null,
				"is_deleted": 0,
				"insert_member": "관리자",
				"update_member": null,
				"insert_date_ymd": "2023-06-29",
				"udate_date_ymd": null,
				"useing_text": "미사용"
			}
			]
			<td>
				<span style="font-family: 'NanumSquareNeo BD' ">{{ item.title }}</span>
				</br>
				<span style="font-family: 'NanumSquareNeo LT' ; font-size:11px;">{{ item.factor_desc }}</span>	
				<br>
				<ul class="list-inline list-inline-skills mg-b-0">
					<li class="list-inline-item"><a href="">	{{ item.practice_unit_nm }}</a></li>
					<li class="list-inline-item"><a href="">{{ item.factor_unit_nm }}</a></li>
					<li class="list-inline-item"><a href="">{{ item.factor_jugi_nm }}</a></li>
				</ul>
			
			</td>
			<th data-orderable="false" data-visible="true" width="40px">#</th>
									<th data-orderable="false" data-visible="false" width="">brd_id</th>
									<th data-orderable="false" data-visible="false" width="">bgr_id</th>

									<th class="d-none d-md-table-cell" data-orderable="false" data-visible="true" width="10%">게시판그룹</th>
									<th class="d-none d-md-table-cell" data-orderable="false" data-visible="true" width="10%">게시판주소</th>
									<th data-orderable="false" data-visible="true" width="">게시판명</th>

									<th data-orderable="false" data-visible="false" width="">brd_mobile_name</th>

									<th class="d-none d-sm-table-cell" data-orderable="true" data-visible="true" width="10%">순서</th>
									<th data-orderable="false" data-visible="false" width="">brd_search</th>
									<th data-orderable="false" data-visible="false" width="">useing</th>
									<th data-orderable="false" data-visible="false" width="">is_deleted</th>

									<th class="d-none d-sm-table-cell" data-orderable="false" data-visible="true" width="10%">사용</th>
									<th data-orderable="false" data-visible="true" width="40px"></th>
			-->
				<table id="list" class="table cell-border hover " style="table-layout: fixed" width="100%">
					<thead>
						<tr>
							<!--0 -->
							<th data-orderable="false" data-visible="true" width="10%">#</th>
							<!--1 -->
							<th data-orderable="true" data-visible="false" width="45px">상태</th>
							<!--2 -->
							<th data-orderable="false" data-visible="true" width="70%">견적내역</th>
							<!--3 -->
							<th data-orderable="false" data-visible="false" width="">금액</th>
							<!--4 -->
							<th data-orderable="true" data-visible="false" width="">등록일</th>
							<!--5 -->
							<th data-orderable="true" data-visible="false" width="">bs_platform</th>
							<!--6 -->
							<th data-orderable="true" data-visible="false" width="">customer_no</th>
							<!--7 -->
							<th data-orderable="true" data-visible="false" width="">staff_no</th>
							<!--8 -->
							<th data-orderable="false" data-visible="false" width="">status</th>
							<!--9 -->
							<th data-orderable="false" data-visible="false" width="">is_open</th>
							<!--10 -->
							<th data-orderable="false" data-visible="false" width="">checkin_local</th>
							<!--11 -->
							<th data-orderable="false" data-visible="false" width="">useing</th>
							<!--12 -->
							<th data-orderable="false" data-visible="false" width="">insert_date_ymd</th>
							<!--13 -->
							<th data-orderable="false" data-visible="false" width="">guest_name</th>
							<!--14 -->
							<th data-orderable="false" data-visible="false" width="">is_deleted</th>
							<th data-orderable="false" data-visible="true" width="15%"></th>
						</tr>
					</thead>
					<tbody style="font-size:13.5px">
						<tr v-for="(item, idx) in list">
							<td align="center">{{ idx + 1}}</td>
							<td align="center">{{ item.status_text }}</td>
							<td align="left">
								<ul class="list-inline list-inline-status mg-b-2">
									<li v-if="item.status == 'RW' " class="list-inline-item"><a class="btn-warning">{{ item.status_text }}</a></li>
									<li v-if="item.status == 'RR' " class="list-inline-item"><a class="btn-primary">{{ item.status_text }}</a></li>
									<li v-if="item.status == 'CR' " class="list-inline-item"><a class="btn-success">{{ item.status_text }}</a></li>
									<li v-if="item.status == 'RC' " class="list-inline-item"><a class="btn-danger">{{ item.status_text }}</a></li>
									<li v-if="item.status == 'RD' " class="list-inline-item"><a class="btn-danger">{{ item.status_text }}</a></li>
									<li v-if="item.status == 'NS' " class="list-inline-item"><a class="btn-info">{{ item.status_text }}</a></li>
								</ul>
								<div style="font-family: 'NanumSquareNeo BD'" class="mg-b-0">
									{{ item.guest_name }}
								</div>

								<template v-if="item.guest_email">
									<span class="mg-b-15" style="font-family: 'NanumSquareNeo LT' ; font-size:12px;">{{ item.guest_email }} </span><br>
								</template>

								<div v-if="item.room.total_rows > 0" class="mg-b-2">
									<div>[숙박] {{$common.formatMoney(item.room_amount)}}</div>
									<template v-for="(item1, idx) in item.room.list">
										<span class="pd-l-5" style="font-family: 'NanumSquareNeo LT' ; font-size:12px;">{{ item1.room_type_nm_short }} {{ item1.exp_ci_date }}-{{ item1.exp_co_date }}({{ item1.nights }}박) {{ item1.rooms }}채</span><br>
									</template>

								</div>

								<div v-if="item.rental_car.total_rows > 0 " class="mg-b-2">
									<div>[렌트차량] {{$common.formatMoney(item.rental_car_amount)}}</div>
									<template v-for="(item2, idx) in item.rental_car.list">
										<span class="pd-l-5" style="font-family: 'NanumSquareNeo LT' ; font-size:12px;">{{ item2.model_name }} {{ item2.name }} / {{ item2.rental_counts }}대 / {{ item2.rental_days }}일</span><br>
									</template>

								</div>

								<div v-if="item.golf.total_rows > 0 " class="mg-b-2">
									<div>[골프] {{$common.formatMoney(item.golf_amount)}}</div>
									<template v-for="(item3, idx) in item.golf.list">
										<span class="pd-l-5" style="font-family: 'NanumSquareNeo LT' ; font-size:12px;">{{ item3.golf_name }} / {{ item3.golf_date }} / {{ item3.golf_people }}명 </span><br>
									</template>

								</div>
								<div v-if="item.item_estimate.length > 0 " class="mg-b-2">
									<div>[기타여행] {{$common.formatMoney(item.item_amount)}}</div>
									<template v-for="(item4, idx) in item.item_estimate">
										<span class="pd-l-5" style="font-family: 'NanumSquareNeo LT' ; font-size:12px;">{{ item4.item_name }} / {{ item4.date }} / {{ item4.people }}명 </span><br>
									</template>

								</div>


								<template v-if="item.total_amount > 0"> [총비용] {{$common.formatMoney(item.total_amount)}} </template>
								<template v-if="item.resfee > 0"> [예약금] {{$common.formatMoney(item.resfee)}} </template>

								<ul class="list-inline list-inline-skills mg-b-0">
									<li v-if="item.bs_platform" class="list-inline-item"><a class="btn-info">{{item.bs_platform_nm}}</a></li>
									<!-- <li v-if="item.checkin_local == 1 " class="list-inline-item"><a href="">현지체크인</a></li>
									<li v-if="item.air_schedule !== '' " class="list-inline-item"><a href="">{{ item.air_schedule }}</a></li> -->
									<li class="list-inline-item"><a href=""> {{ item.is_open_text }}견적</a></li>
								</ul>

								<span class="mg-t-15" style="font-family: 'NanumSquareNeo LT' ; font-size:12px;">등록일자:{{ item.insert_date }} </span><br>




							</td>
							<td align="center">{{ item.total_amount }}{{ item.resfee }}</td>
							<td align="center">{{ item.insert_date }}</td>
							<td align="center">{{ item.bs_platform }}</td>
							<td align="center">{{ item.customer_no }}</td>
							<td align="center">{{ item.staff_no }}</td>
							<td align="center">{{ item.status }}</td>
							<td align="center">{{ item.is_open }}</td>
							<td align="center">{{ item.checkin_local }}</td>
							<td align="center">{{ item.useing }}</td>
							<td align="center">{{ item.insert_date_ymd }}</td>
							<td align="center">{{ item.guest_name }}</td>
							<td align="center">{{ item.is_deleted }}</td>
							<td align="center">
								<a class="btn btn-xs btn-outline-danger" @click="go_detail('list',item.no, idx)" href="javascript:void(0);"> <i data-feather="edit"></i> </a>
								<a class="btn btn-xs btn-outline-success" @click="$common.go_detail('list',item.no, idx,'invoice')" href="javascript:void(0);"> <i data-feather="paperclip"></i> </a>

							</td>

						</tr>
					</tbody>
				</table>
			</div><!-- container -->
		</div>



		<!-- modal start -->
		<div class="modal fade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content tx-14">
					<div class="modal-header">
						<h6 class="modal-title">선택된 생활요소를 기록하시겠습니까?</h6>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<p class="modal-message mg-b-0">
						<h6 class="modal-target-title">{{selected.title}}</h6>

						<small class="modal-target-detail"> {{selected.factor_class_nm}} / 입력단위 :{{selected.factor_class_nm}} / 입력주기 : {{selected.factor_jugi_nm}}</small>
						</p>

						<div class="search_area">
							<div class="row">

								<div class="form-group col-lg-6 unit_input_date_m">
									<label for="formGroupExampleInput" class="d-block">실천일자</label>
									<input type="text" class="form-control " placeholder="실천일자 선택" id="action_datepick_m">
								</div>

								<div class="form-group col-lg-2" hidden>
									<label for="formGroupExampleInput" class="d-block">일수</label>
									<input type="text" class="form-control day_count" disabled placeholder="일수" v-model="selected.day_count">
									<input type="hidden" class="form-control " v-model="selected.startDate">
									<input type="hidden" class="form-control " v-model="selected.endDate">

								</div>
								<div :class="{
									'form-group col-lg-4 unit_type_1': selected.factor_unit_nm === '액수',
									'form-group col-lg-3 unit_type_1': selected.factor_unit_nm !== '액수',
									'd-none': ['패스', '유무'].includes(selected.factor_unit_nm)
									}">
									<label for="formGroupExampleInput" class="d-block">{{selected.factor_unit_nm}}</label>
									<div class="input-group">
										<input type="text" class="form-control text-right" placeholder="입력" :value="selected.unit || 1" @input="selected.unit = $event.target.value">
										<span v-if="selected.factor_unit_nm === '액수'" class="input-group-text" style="font-size: 12px;">원</span>
									</div>
								</div>
							</div>
							<div class="row">

								<div class="form-group col-lg-12">
									<label for="formGroupExampleInput" class="d-block">메모</label>
									<input type="text" class="form-control " placeholder="메모" v-model="selected.bigo_1">
								</div>


							</div>
						</div>



					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary tx-13" @click="achive_merit()">저장하기</button>

					</div>
				</div>
			</div>
		</div>
	</div><!-- row -->


	<script>
		// <!-- vue unyict lib -->
		$.getScript("/assets/js/unyict/common-lib_v1_6.js", function() {
			//로드 완료 후 로컬 js 로드.
			$.getScript("/views/dfadmin/basic/estimate/estimatelist/js/list.js");
			//$.getScript("/views/dfadmin/basic/estimate/estimatelist/js/write.js");
		});
		$(document).ready(function() {
			// 여기에 다른 초기화 .
		});
	</script>
