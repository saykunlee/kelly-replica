<style>
	/* Add full-width class to ensure inputs take full container width */
	.full-width {
		width: 100%;
		box-sizing: border-box;
		/* Include padding and border in width */
	}

	/* Flexbox for responsive layout management */
	.row {
		display: flex;
		flex-wrap: wrap;
		margin-right: -15px;
		margin-left: -15px;
	}

	/* Override specific device styles */
	@media screen and (max-width: 768px) {

		.col-lg-6,
		.col-lg-2,
		.col-6,
		.col-4 {
			padding-right: 15px;
			padding-left: 15px;
			flex: 0 0 50%;
			max-width: 50%;
		}

		.col-lg-2.col-4 {
			flex: 0 0 33.333333%;
			max-width: 33.333333%;
		}
	}

	.form-group {
		margin-bottom: 1rem;
	}

	/* Apply border box for all elements */
	*,
	*::before,
	*::after {
		box-sizing: inherit;
	}

	html {
		box-sizing: border-box;
	}

	/* Ensure Safari on iPhone displays inputs correctly */
	@media screen and (-webkit-min-device-pixel-ratio: 0) {

		input[type="date"],
		select {
			width: 100%;
			-webkit-appearance: none;
			appearance: none;
		}
	}

	/* Style for the button to ensure consistency */
/* 	.btn {
		display: inline-block;
		font-weight: 400;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		user-select: none;
		border: 1px solid transparent;
		padding: 0.375rem 0.75rem;
		font-size: 1rem;
		line-height: 1.5;
		border-radius: 0.25rem;
		transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
	} */
</style>

<hr class="mg-t-10 mg-b-20">
<div id="app">

	<div class="search_area">
		<div class="row">
			<div class="col-lg-6 col-6">
				<div class="form-group">
					<label for="exp_date" class="d-block">체크인</label>
					<input hidden type="text" id="exp_date" name="exp_date" class="form-control tx-center wd-100p" placeholder="숙박일정">
					<input type="date" class="form-control tx-center full-width" placeholder="체크인" v-model="search.sel_exp_ci_date">
				</div>
			</div>
			<div class="col-lg-6 col-6">
				<div class="form-group">
					<label for="exp_date" class="d-block">체크아웃</label>
					<input type="date" class="form-control tx-center full-width" placeholder="체크아웃" v-model="search.sel_exp_co_date">
				</div>
			</div>

			<div class="col-lg-2 col-4">
				<!-- 풀빌라 채 선택 -->
				<div class="form-group">
					<label for="commoncode_1_select" class="d-block">빌라</label>
					<select class="custom-select tx-center full-width" id="commoncode_1_select" v-model="search.sel_rooms">
						<option value="1">1채</option>
						<option value="2">2채</option>
						<option value="3">3채</option>
						<option value="4">4채</option>
						<option value="5">5채</option>
					</select>
				</div>
			</div>

			<div class="col-lg-2 col-4">
				<!-- 인 -->
				<div class="form-group">
					<label for="commoncode_2_select" class="d-block">인</label>
					<select class="custom-select tx-center full-width" id="commoncode_2_select" style="height: calc(1.5em + 0.9079rem);" v-model="search.sel_guests">
						<option value="1">1인</option>
						<option value="2">2인</option>
						<option value="3">3인</option>
						<option value="4" selected>4인</option>
						<option value="5">5인</option>
						<option value="6">6인</option>
						<option value="7">7인</option>
						<option value="8">8인</option>
						<option value="9">9인</option>
						<option value="10">10인</option>
					</select>
				</div>
			</div>

			<div class="col-lg-2 col-4">
				<div class="form-group">
					<label for="formGroupExampleInput" class="d-block">&nbsp;</label>
					<a class="btn btn-block btn-primary" @click="getavailableroom_autoselect()" href="javascript:void(0);"> 검색</a>
				</div>
			</div>
		</div>

	</div>

	<!-- 숙박예약 -->
	<div class="mg-b-15">
		<div class="mg-b-0 d-flex flex-row justify-content-between">
			<div class="align-self-center">
				<h5 class="mg-t-10" style="font-family:'NanumSquareNeo BD'">예약정보</h5>
			</div>
			<div class="align-self-center d-none"><a class="btn btn-sm btn-outline-primary" href="#roomSelectModal" data-toggle="modal"> <i data-feather="edit" class="mg-r-5"></i>
					<template v-if="$app_estimate.lib_estimate.room_estimate.room_checked.length > 0">숙박일정 수정 </template>
					<template v-else>숙박일정 추가 </template>
				</a></div>
		</div>
		<hr class="mg-t-0 mg-b-15">
		<!-- 객실검색결과 -->
		<!-- loding list -->
		<div v-if="is_loding_list_1" class="row">

			<div class="col-lg-12">
				<div class="placeholder-paragraph ">
					<div class="line"></div>
					<div class="line"></div>
				</div>
			</div>
		</div>
		<!-- loding list end -->
		<template v-else>
			<template v-if="searched_rooms.count_total < 1">
				<div v-if="is_room_searched" class="alert alert-danger " role="alert">
					해당 조건으로 예약 가능한 객실이 없습니다.
				</div>



				<div v-else-if="$app_estimate.lib_estimate.room_estimate.room_checked.length < 1" class="alert alert-primary	" role="alert">
					예약일정을 검색해 해주세요.
				</div>

			</template>

			<template v-if="$app_estimate.lib_estimate.room_estimate.room_checked.length > 0">

				<template>
					<div v-for="(item1, index) in $app_estimate.lib_estimate.room_estimate.room_checked" class="row mg-b-10 ">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex flex-row justify-content-between tx-neobd">
							<!-- 숙소명 -->
							<div class="tx-11">[{{item1.room_type_nm_short}}]</div>
							<!-- 기간(박)/채/인 -->
							<div class="tx-11">{{ item1.exp_ci_date }}-{{ item1.exp_co_date }}({{ item1.nights }}박)/{{ item1.rooms }}채/{{ search.sel_guests }}인</div>
							<!-- 금액 -->
							<div class="d-none">{{$common.formatMoney(item1.charge_amount_total)}} 원<!-- ({{$common.formatMoney(item1.charge_amount_total_kr)}}) --></div>
						</div>

					</div>
					<!-- 개별 객실 상세 -->
					<!-- estimate.room_estimate.room_checked.0.detail_rooms -->
					<div v-if="$app_estimate.lib_estimate.room_estimate.room_checked[0].detail_rooms.length > 0" class="table-responsive">
						<table class="table table-bordered room_list mg-b-0 tx-center">
							<thead>
								<tr class="tx-neobd">
									<!-- <th scope="col">순번</th> -->
									<th scope="col">숙소명</th>
									<th scope="col">체크인</th>
									<th scope="col">체크아웃</th>
									<th scope="col">박수</th>
									<th scope="col">인원</th>
									<th scope="col">객실</th>
									<th scope="col">변경</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item_room, index_room) in $app_estimate.lib_estimate.room_estimate.room_checked[0].detail_rooms" v-if="item_room.is_master == '1'">

									<!-- <th scope="row">{{item_room.room_group}} </th> -->
									<td>{{$app_estimate.lib_estimate.room_estimate.room_checked[0].room_type_nm_short}}</td>
									<td>{{item_room.exp_ci_date}}</td>
									<td>{{item_room.exp_co_date}}</td>
									<td>{{$app_estimate.lib_estimate.room_estimate.res_info.nights}}</td>
									<td>{{$app_estimate.lib_estimate.guests}}</td>
									<td>{{item_room.room_number}}</td>
									<td><a class="btn btn-xs btn-outline-success" @click="open_assign_room_modal($app_estimate.lib_estimate.room_estimate.res_info.reservation_no,item_room.room_group,item_room.room_no)" href="javascript:void(0);">호실변경 </a></td>

								</tr>

							</tbody>
						</table>
					</div>
					<!-- 객실변경 -->
					<table v-if="showRommChange" class="table table-bordered  mg-t-20 mg-b-20 tx-left tx-12">
						<thead>
							<tr class="tx-neobd bg-primary-light">
								<th>호실변경</th>
							</tr>
						</thead>
						<tr>
							<td>
								<div class="form-group">
									<!-- <label for="room_type_no" class="control-label">[{{ $app_estimate.lib_estimate.room_estimate.room_checked[0].room_type_nm_short }}]</label> -->

									<div class=" btn-group-toggle" data-toggle="buttons">
										<template v-for="assignable_rooms_item in assignable_rooms.room_list">

											<label v-if="selectedRoom && selectedRoom.origin_room_no === assignable_rooms_item.no" class="btn btn-outline-primary no-border active-style mg-5" :disabled="assignable_rooms_item.can_assign !== 1">
												<input type="radio" :value="assignable_rooms_item.no" v-model="selectedRoom.room_no" :disabled="assignable_rooms_item.can_assign !== 1" @click="assign_selectRoom(assignable_rooms_item)">
												{{ assignable_rooms_item.room_number }}
											</label>
											<label v-else class="btn btn-outline-primary no-border mg-5" :class="{ 'active-style': selectedRoom && selectedRoom.room_no === assignable_rooms_item.no, 'disabled-background': assignable_rooms_item.can_assign !== 1 }" :disabled="assignable_rooms_item.can_assign !== 1">
												<input type="radio" :value="assignable_rooms_item.no" v-model="selectedRoom.room_no" :disabled="assignable_rooms_item.can_assign !== 1" @click="assign_selectRoom(assignable_rooms_item)">
												{{ assignable_rooms_item.room_number }}
											</label>
										</template>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary btn-small tx-12" @click="showRommChange = false">취소</button>
									<button type="button" class="btn btn-danger btn-small tx-12" @click="set_change_assign_room()">확인</button>
									<!-- <button type="button" class="btn btn-danger tx-13" @click="change_assign_room()">변경하기</button> -->
								</div>

							</td>
						</tr>
					</table>
					<!-- !객실변경 -->
					<div class="d-block">
						<table class="table col-12 ">
							<tfoot class="tfoot-estmate">
								<tr align="center">
									<th colspan="1">숙박 총 비용</th>
									<th colspan="12" class="">
										<div class="text-right"><!-- right -->

											<div v-if="$app_estimate.lib_estimate.room_amount_kr > 0" class="font-size-sm pt-2 "><span class="text-muted mr-2">{{$common.formatMoney($app_estimate.lib_estimate.room_amount_kr)}} 원</span></div>
											<div v-else class="font-size-sm pt-2 "><span class="text-muted mr-2">{{$common.formatMoney($app_estimate.exchange_kor( $app_estimate.lib_estimate.room_estimate.room_amount))}} 원</span></div>
											<h5 class="product-card-title font-weight-bold border-0 pt-0 pd-10 mb-0">{{$common.formatMoney($app_estimate.lib_estimate.room_amount)}} 페소 </h5>
										</div>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>

				</template>
			</template>
		</template>
	</div>


	<!-- 고객정보 -->
	<div class="mg-b-0 d-flex flex-row justify-content-between mg-t-15">
		<div class="align-self-center">
			<h5 class="mg-t-10" style="font-family:'NanumSquareNeo BD'"> 고객정보 </h5>
		</div>
		<!-- <div class="align-self-center"><a class="btn btn-xs btn-outline-primary" data-toggle="modal" href="#modal_add_room" @click=""> <i data-feather="edit" class="mg-r-5"></i>숙박예약수정</a></div> -->
	</div>
	<hr class="mg-t-0 mg-b-15">
	<div class="row">
		<!-- 예약고객명 -->
		<div class="col-lg-3 col-6 d-flex">
			<div class="form-group flex-fill">
				<label class="control-label">예약상태</label>
				<div class="form-inline">
					<select class="form-control w-100" v-model="$app_estimate.lib_estimate.status" @change="changes_estimate_status()">
						<option value="">선택하세요</option>
						<option value="RW">등록</option>
						<option value="RR">대기</option>
						<option value="CR">확정</option>
						<option value="RC">취소</option>
						<option value="RD">삭제</option>
						<option value="NS">노쇼</option>
					</select>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-6 d-flex">
			<!-- 예약고객명 -->

			<div class="form-group flex-fill">
				<label>예약고객명 </label>
				<input type="text" class="form-control" placeholder="예약고객명 입력" v-model="$app_estimate.lib_estimate.guest_name" required>
			</div>
		</div>
		<!-- 연락처 -->
		<div class="col-lg-3 col-6 d-flex">
			<div class="form-group flex-fill">
				<label>연락처 </label>
				<input type="text" class="form-control" placeholder="연락처 입력" v-model="$app_estimate.lib_estimate.guest_hp" required>
			</div>
		</div>
		<!-- 이메일 -->
		<div class="col-lg-3 col-6 d-flex">
			<div class="form-group  flex-fill">
				<label>이메일 </label>
				<input type="text" class="form-control" placeholder="이메일 입력" v-model="$app_estimate.lib_estimate.guest_email" required>
			</div>
		</div>
	</div>
	<div class="row">
		<!--체크인시간  -->
		<div class="col-lg-3 col-6 d-flex">
			<div class="form-group flex-fill">
				<label class="control-label">체크인</label>
				<div class="form-inline">
					<select class="form-control w-100" v-model="$app_estimate.lib_estimate.exp_ci_time">
						<option value="">선택하세요</option>
						<option value="19:00">19:00 이후</option>
						<option value="01:00">익일 새벽 01:00 이후</option>

					</select>
				</div>
			</div>
		</div>
		<!-- 체크아웃 시간 -->
		<div class="col-lg-3 col-6 d-flex">
			<div class="form-group flex-fill">
				<label class="control-label">체크아웃</label>
				<div class="form-inline">
					<select class="form-control w-100" v-model="$app_estimate.lib_estimate.exp_co_time">
						<option value="">선택하세요</option>
						<option value="10:00">10:00</option>
						<option value="11:00">11:00</option>
						<option value="12:00">12:00</option>
						<option value="13:00">13:00</option>
						<option value="14:00">14:00</option>
						<option value="15:00">15:00</option>
						<option value="16:00">16:00</option>
						<option value="17:00">17:00</option>
						<option value="18:00">18:00</option>
						<option value="19:00">19:00</option>
						<option value="20:00">20:00</option>
						<option value="21:00">21:00</option>
						<option value="22:00">22:00</option>
						<option value="23:00">23:00</option>
					</select>
				</div>
			</div>
		</div>

	</div>
	<div class="d-none row">
		<!-- 현지체크인 -->
		<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 d-flex ">

			<div class="form-group  flex-fill pd-2 ">
				<label> 공항 픽업 요청 *유료</label>
				<select class="custom-select" v-model="$app_estimate.lib_estimate.air_pickup">
					<option value="필요없음">필요 없음</option>
					<option value="클락공항">클락 공항</option>
					<option value="마닐라공항">마닐라 공항</option>
				</select>
			</div>
			<div class="form-group  flex-fill pd-2 ">
				<label>항공스케줄 </label>
				<input type="text" class="form-control" placeholder="항공스케줄 입력" v-model="$app_estimate.lib_estimate.air_schedule">
			</div>
		</div>
		<!-- 고객요구사항 -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex">
			<div class="form-group  flex-fill pd-2 ">
				<label>고객요구사항 </label>
				<input type="text" class="form-control" placeholder="고객요구사항 입력" v-model="$app_estimate.lib_estimate.guest_requirement">
			</div>
		</div>
	</div>

	<hr class=" mg-t-10 mg-b-20">
	<!-- 버튼영역 -->
	<div class="row mg-t-15">
		<div class="col-12">
			<!-- <a class="btn btn-secondary" @click="gp_to_list()" href="javascript:void()">리스트</a> -->
			<button v-if="action_type == 'update' " href="#modal_delete" data-toggle="modal" class="btn btn-danger ">예약삭제</button>
			<button href="#modal_save" data-toggle="modal" class="btn btn-success">저장하기</button>
			<a hidden class="btn btn-primary" href="#" data-toggle="modal" @click="openPopupVue('/invoice/'+$app_estimate.lib_estimate.no + '/1')"> <i data-feather="clipboard" class="mg-r-5"></i>미리보기</a>

		</div>
	</div>

	<hr class="d-none mg-t-10 mg-b-40">


	<!-- total amount area start -->
	<!-- <hr class="mg-t-0"> -->
	<table class="d-none table mg-b-0 mg-t-40">
		<tfoot class="tfoot-estmate">
			<tr align="center">
				<th>
					<h5 class="tx-neobd"> 총 여행비용 </h5>
				</th>
				<th colspan="12" class="">
					<div class="text-right"> <!-- right -->
						<template v-if="$app_estimate.lib_estimate.dc_amount > 0 || $app_estimate.lib_estimate.adjust_amount > 0">
							<div class="font-size-sm"><span class="mr-3">{{$common.formatMoney($app_estimate.lib_estimate.pre_amount)}} 원</span></div>
							<div v-if="$app_estimate.lib_estimate.dc_amount > 0" class="font-size-sm"><span class="tx-danger mr-3">- {{$common.formatMoney($app_estimate.lib_estimate.dc_amount)}} 원</span></div>
							<div v-if="$app_estimate.lib_estimate.adjust_amount > 0" class="font-size-sm"><span class="tx-success mr-3">- {{$common.formatMoney($app_estimate.lib_estimate.adjust_amount)}} 원</span></div>
							<hr>
						</template>
						<div class="font-size-sm">
							<span v-if="$app_estimate.lib_estimate.total_amount_kr > 0" class="text-muted mr-3">{{$common.formatMoney($app_estimate.lib_estimate.total_amount_kr)}} </span>
							<span v-else class="text-muted mr-3">{{$common.formatMoney($app_estimate.exchange_kor($app_estimate.lib_estimate.total_amount))}} </span>
						</div>
						<h4 class="product-card-title font-weight-bold border-0 pt-0 pb-0 mb-0 mr-3">{{$common.formatMoney($app_estimate.lib_estimate.total_amount)}} 원 </h4>
					</div>
					<!-- {{$common.formatMoney(rental_car_amount)}} 원< -->
				</th>
			</tr>
		</tfoot>
	</table>
	<!-- total amount area end -->

	<div hidden class="row mg-t-10">
		<div class="col-lg-12">
			<div v-if="action_type == 'update' " class="form-group">
				<label class=" control-label">수정이력</label>
				<div class="table-responsive table-striped ">
					<table class="table table-sm mg-b-0 ">
						<thead>
							<tr>
								<th scope="col">입력일시</th>
								<th scope="col">입력자</th>
								<th scope="col">수정일시</th>
								<th scope="col">수정자</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>{{$app_estimate.lib_estimate.insert_date}}</td>
								<td>{{$app_estimate.lib_estimate.insert_member}}</td>
								<td>{{$app_estimate.lib_estimate.update_date}}</td>
								<td>{{$app_estimate.lib_estimate.update_member}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<!-- modal start -->
	<div class="modal fade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content tx-14">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">{{$common.toastr_project}}</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p class="mg-b-0">저장하시겠습니까? </p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary tx-13" @click="save_estimate();">저장하기</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content tx-14">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">{{$common.toastr_project}}</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p class="mg-b-0">삭제하시겠습니까?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-danger tx-13" @click="delete_estimate()"><i class="far fa-trash-alt"></i></button>
				</div>
			</div>
		</div>
	</div>

	<!-- assign_room_modal -->
	<div class="modal fade" id="assign_room_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content tx-14">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">객실변경</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<!-- <label for="room_type_no" class="control-label">[{{ $app_estimate.lib_estimate.room_estimate.room_checked[0].room_type_nm_short }}]</label> -->

						<div class=" btn-group-toggle" data-toggle="buttons">
							<template v-for="assignable_rooms_item in assignable_rooms.room_list">

								<label v-if="selectedRoom && selectedRoom.origin_room_no === assignable_rooms_item.no" class="btn btn-outline-primary no-border active-style mg-5" :disabled="assignable_rooms_item.can_assign !== 1">
									<input type="radio" :value="assignable_rooms_item.no" v-model="selectedRoom.room_no" :disabled="assignable_rooms_item.can_assign !== 1" @click="assign_selectRoom(assignable_rooms_item)">
									{{ assignable_rooms_item.room_number }}
								</label>
								<label v-else class="btn btn-outline-primary no-border mg-5" :class="{ 'active-style': selectedRoom && selectedRoom.room_no === assignable_rooms_item.no, 'disabled-background': assignable_rooms_item.can_assign !== 1 }" :disabled="assignable_rooms_item.can_assign !== 1">
									<input type="radio" :value="assignable_rooms_item.no" v-model="selectedRoom.room_no" :disabled="assignable_rooms_item.can_assign !== 1" @click="assign_selectRoom(assignable_rooms_item)">
									{{ assignable_rooms_item.room_number }}
								</label>
							</template>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">취소하기</button>
						<button type="button" class="btn btn-danger tx-13" @click="set_change_assign_room()">확인</button>
						<!-- <button type="button" class="btn btn-danger tx-13" @click="change_assign_room()">변경하기</button> -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- select room Modal -->
	<div class="modal fade" id="roomSelectModal" tabindex="-1" role="dialog" aria-labelledby="roomSelectModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">{{$common.toastr_project}}</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- room search area start-->
					<div class="search_area_sm mg-b-3">
						<div class="row">
							<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex">
								<!-- 숙박일정 -->
								<div class="pd-2 flex-grow-1">
									<label for="exp_date" class="d-block ">숙박일정</label>
									<input type="text" id="exp_date" name="exp_date" class="form-control hasDatepicker tx-center wd-100p" placeholder="숙박일정">
								</div>
							</div>
							<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex">
								<!-- 풀빌라 채 선택 -->
								<div class="pd-2 flex-fill ">
									<label for="commoncode_1_select" class="d-block">빌라</label>
									<select class="custom-select tx-center" id="commoncode_1_select" v-model="search.sel_rooms">
										<option value="1">1채</option>
										<option value="2">2채</option>
										<option value="3">3채</option>
										<option value="4">4채</option>
										<option value="5">5채</option>
									</select>
								</div>
								<!-- 인 -->
								<div class="pd-2 flex-fill ">
									<label for="commoncode_2_select" class="d-block">인</label>
									<select class="custom-select tx-center" id="commoncode_2_select" v-model="search.sel_guests">
										<option value="1">1인</option>
										<option value="2">2인</option>
										<option value="3">3인</option>
										<option value="4" selected>4인</option>
										<option value="5">5인</option>
										<option value="6">6인</option>
										<option value="7">7인</option>
										<option value="8">8인</option>
										<option value="9">9인</option>
										<option value="10">10인</option>
									</select>
								</div>
								<!-- 숙소 검색하기 -->
								<div class="pd-2 flex-fill ">
									<label for="formGroupExampleInput" class="d-block">&nbsp</label>
									<button type="button" class="btn btn-info btn-block" @click="getavailableroom()"><i data-feather="search"></i> 검색</button>
								</div>
							</div>
						</div>
					</div>

					<!-- loding list -->
					<div v-if="is_loding_list_1" class="row">
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
					<!-- loding list end -->

					<template v-else>
						<template v-if="searched_rooms.count_total < 1">
							<div v-if="is_room_searched" class="alert alert-danger " role="alert">
								해당 조건으로 예약 가능한 객실이 없습니다.
							</div>
							<div v-else class="alert alert-info	" role="alert">
								하시는 여행 일정을 검색해주세요.
							</div>
						</template>
						<template v-else>
							<div class="table-responsive">
								<table id="ab_room_list" class="table table-sm table align-self-center" width="100%">
									<tbody>
										<tr v-for="(item, idx) in searched_rooms.result_ab_room_type" :class="[(checkedRes.includes(item.room_type_no) ? 'active_row' : ''), $app_estimate.getColor(item) ]" :style="{ 'background-color': $app_estimate.getColor_background(item) }">
											<!--  <td align="center">{{ idx + 1 }}</td>  -->
											<td align="left">
												<div class="row">
													<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 d-flex">
														<div class="flex-fill">
															{{ item.room_name }}
														</div>
													</div>
													<div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 d-flex">
														<div class="flex-fill">{{ item.check_in }}-{{ item.check_out }}({{ item.req_nights }}박)/{{ item.req_rooms }}채/{{ search.sel_guests }}인</div>
													</div>
													<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 d-flex">
														<div class="flex-fill">{{$common.formatMoney(item.charge_amount_total)}} ({{$common.formatMoney(item.charge_amount_total_kr)}})</div>
													</div>
												</div>
											</td>
											<td align="center">
												<span v-if="(item.is_available == 0 || item.is_room_assignable == 0 )  && item.allow_overbooking == 1" class="badge badge-danger">over</span>
											</td>
											<td class="pd-l-0" style="line-height: 3.5;" align="center">
												<div v-if="(item.is_available == 0 || item.is_room_assignable == 0 ) && item.allow_overbooking == 0" class="custom-control custom-checkbox">
													<label class="btn btn-xs btn-light mg-b-0" style="min-width: 60px;">예약 불가</label>
												</div>
												<div v-else class="custom-control custom-checkbox">
													<!-- <input hidden type="checkbox" :id="'customCheck' + idx" :value="item.room_type_no" v-model="checkedRes" @change="$app_estimate.selectRoom"> -->
													<input hidden type="checkbox" :id="'customCheck' + idx" :value="item.room_type_no" v-model="checkedRes" @change="$app_estimate.selectRoom" :disabled="checkedRes.length > 0 && !checkedRes.includes(item.room_type_no)">
													<label :class="[checkedRes.length > 0 && !checkedRes.includes(item.room_type_no) ? 'btn btn-xs btn-light mg-b-0' : 'btn btn-xs btn-success mg-b-0']" :for="'customCheck' + idx" style="min-width: 60px;">{{ checkedRes.includes(item.room_type_no) ? '선택 해제' : '예약 가능' }}</label>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<!-- search area end -->
						</template>
					</template>


					<div class="modal-footer">
						<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
						<!-- <button type="button" class="btn btn-primary tx-13" @click="select_room();">선택완료</button> -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- model end -->
</div>

<script>
	// <!-- vue unyict lib -->
	$.getScript("/assets/js/unyict/common-lib_v1_6.js", function() {
		$.getScript("/assets/js/unyict/estimate-lib.js", function() {
			//로드 완료 후 로컬 js 로드.
			$.getScript("/views/dfadmin/basic/estimate/estimatelist/js/write_room.js");
		});
	});
	$(document).ready(function() {
		// 여기에 다른 초기화 .
	});
</script>