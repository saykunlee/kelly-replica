<hr class="mg-t-10 mg-b-20">
<div id="app">

	<div class="row">
		<!-- 파트너사 -->
		<div class="mg-b-10 col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex">
			<div class="pd-2 flex-fill">
				<label>파트너사<span class="tx-danger"></span></label>
				<select class="form-control select_3" id="commoncode_2_select" style="width: 100%;" v-model="$app_estimate.lib_estimate.bs_platform">
					<option value="">파트너사를 선택하세요</option>
					<option v-for="(code, idx) in $app_estimate.lib_partners" :value="code.no">
						{{ code.name }}
						<template v-if="code.useing !== '1' ">(X)</template>
					</option>
				</select>
			</div>

			<!-- 견적상태 -->
			<div class="pd-2 flex-fill">
				<label class=" control-label">견적상태</label>
				<div class="form-inline">
					<select class="form-control w-100" v-model="$app_estimate.lib_estimate.status">
						<option value="">선택하세요</option>
						<option value="RR">대기</option>
						<option value="CR">확정</option>
						<option value="RC">취소</option>
						<option value="RD">삭제</option>
						<option value="NS">노쇼</option>
					</select>
				</div>
			</div>
			<!-- 미리보기 -->
			<div class="pd-2 flex-fill">
				<label class=" control-label">&nbsp;</label>
				<div class="form-inline">
					<a class="btn btn-outline-primary" style="min-width:110px" href="#" data-toggle="modal" @click="openPopupVue('/invoice/'+$app_estimate.lib_estimate.no + '/1')"> <i data-feather="clipboard" class="mg-r-5"></i>미리보기</a>
				</div>
			</div>

		</div>

		<div class="col-lg-12">
			<div class="form-group">
				<label for="notes" class="control-label">고객안내문</label>
				<textarea class="form-control" id="notes" rows="5" placeholder="안내문 입력" v-model="$app_estimate.lib_estimate.remark_1"></textarea>
			</div>
		</div>
	</div>

	<!-- 고객정보 -->
	<div class="mg-b-0 d-flex flex-row justify-content-between">
		<div class="align-self-center">
			<h5 class="mg-t-10" style="font-family:'NanumSquareNeo BD'"> 고객정보 </h5>
		</div>
		<!-- <div class="align-self-center"><a class="btn btn-xs btn-outline-primary" data-toggle="modal" href="#modal_add_room" @click=""> <i data-feather="edit" class="mg-r-5"></i>숙박견적수정</a></div> -->
	</div>
	<hr class="mg-t-0 mg-b-15">
	<div class="row">
		<!-- 견적고객명 -->
		<div class="col-lg-4  col-md-12 col-sm-12 col-xs-12 d-flex ">
			<div class="form-group flex-fill">
				<label>견적고객명 </label>
				<input type="text" class="form-control" placeholder="견적고객명 입력" v-model="$app_estimate.lib_estimate.guest_name" required>
			</div>
		</div>
		<!-- 연락처 -->
		<div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 d-flex">
			<div class="form-group flex-fill pd-2 ">
				<label>연락처 </label>
				<input type="text" class="form-control" placeholder="연락처 입력" v-model="$app_estimate.lib_estimate.guest_hp" required>
			</div>
			<!-- 이메일 -->
			<div class="form-group  flex-fill pd-2 ">
				<label>이메일 </label>
				<input type="text" class="form-control" placeholder="이메일 입력" v-model="$app_estimate.lib_estimate.guest_email" required>
			</div>
		</div>
	</div>
	<div class="row">
		<!-- 현지체크인 -->
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex">
			<!-- <small class="form-text text-danger"> *전체 일정에 맞춰 차량 렌트 시, 새벽 시간 클락 공항 픽업 및 드랍 무료</small> -->
			<!-- 항공스케줄 -->
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
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex">
			<div class="form-group  flex-fill pd-2 ">
				<label>고객요구사항 </label>
				<input type="text" class="form-control" placeholder="고객요구사항 입력" v-model="$app_estimate.lib_estimate.guest_requirement">
			</div>
		</div>
	</div>

	<!-- 고객정보 -->

	<!-- 숙박견적 -->
	<div class="mg-b-0 d-flex flex-row justify-content-between">
		<div class="align-self-center">
			<h5 class="mg-t-10" style="font-family:'NanumSquareNeo BD'"> 숙박견적 </h5>
		</div>
		<div class="align-self-center"><a class="btn btn-sm btn-outline-primary" href="#roomSelectModal" data-toggle="modal"> <i data-feather="edit" class="mg-r-5"></i>
				<template v-if="$app_estimate.lib_estimate.room_estimate.room_checked.length > 0">숙박일정 수정 </template>
				<template v-else>숙박일정 추가 </template>
			</a></div>
	</div>
	<hr class="mg-t-0 mg-b-15">
	<template v-if="$app_estimate.lib_estimate.room_estimate.room_checked.length > 0">

		<div v-for="(item1, index) in $app_estimate.lib_estimate.room_estimate.room_checked" class="row mg-b-10 ">
			<!-- 숙소명 -->
			<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 d-flex flex-row justify-content-between">
				<div class="flex-fill strong">[{{item1.room_type_nm_short}}]</div>
			</div>
			<!-- 기간(박)/채/인원 -->
			<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex flex-row justify-content-between">
				<div class="flex-fill">{{ item1.exp_ci_date }}-{{ item1.exp_co_date }}({{ item1.nights }}박)/{{ item1.rooms }}채/{{ search.sel_guests }}인</div>
			</div>
			<!-- 금액 -->
			<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 d-flex flex-row justify-content-between">
				<div class="flex-fill strong">{{$common.formatMoney(item1.charge_amount_total)}} 페소({{$common.formatMoney(item1.charge_amount_total_kr)}}원)</div>
			</div>

		</div>
		<div class="row">

			<table class="table col-12">
				<tfoot class="tfoot-estmate">
					<tr align="center">
						<th colspan="1">숙박 총 비용</th>
						<th colspan="12" class="">
							<div class="text-right"><!-- right -->
								<div class="font-size-sm pt-2 "><span class="text-muted mr-2">{{$common.formatMoney($app_estimate.exchange_kor( $app_estimate.lib_estimate.room_estimate.room_amount))}}원</span></div>
								<h5 class="product-card-title font-weight-bold border-0 pt-0 pd-10 mb-0">{{$common.formatMoney($app_estimate.lib_estimate.room_amount)}} 페소 </h5>
							</div>

						</th>

					</tr>
				</tfoot>
			</table>
		</div>
	</template>
	<template v-else>
		<div class="row">

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex flex-row justify-content-between">

				<div class="alert alert-info flex-fill " class="pd-l-0 pd-r-0" role="alert"> 숙박일정을 추가해주세요. </div>
			</div>
			<!-- <div class="align-self-center mg-b-10"><a class="btn btn-outline-danger btn-block" href="#roomSelectModal" data-toggle="modal" @click="">숙박조회</a></div> -->
		</div>

	</template>

	<!-- !숙박견적 -->


	<div class="mg-b-0 d-flex flex-row justify-content-between">
		<div class="align-self-center">
			<h5 class="mg-t-10" style="font-family:'NanumSquareNeo BD'"> 차량견적 </h5>
		</div>
		<div class="align-self-center"><a class="btn btn-sm btn-outline-primary" href="javascript:void(0);" @click="$app_estimate.addRow"> <i data-feather="plus" class="mg-r-5"></i>차량견적추가</a></div>
	</div>
	<!-- 차량견적 -->
	<div class="collapse mg-t-5 mb-3 show" id="Selection-rentcar">
		<div class="row mt-2">
			<!-- list area start -->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="media">
					<div class="media-body">
						<table class="table mg-b-0">
							<thead hidden class="thead-estmate">
								<tr align="center">
									<th scope="col" width="">차량 및 </th>
									<th scope="col" width="20%">일수</th>
									<th scope="col" width="20%">비용</th>
									<th scope="col" width="10%"></th>
								</tr>
							</thead>
							<tbody>
								<template v-if="$app_estimate.lib_estimate.rental_car_estimate.length < 1">
									<tr>
										<td colspan="13" class="pd-l-0 pd-r-0">
											<div class="alert alert-info" role="alert"> 렌트차량을 추가해주세요. </div>
										</td>
									</tr>
								</template>
								<template v-else>
									<tr v-for="(car, index) in $app_estimate.lib_estimate.rental_car_estimate" :key="index">
										<td colspan="13">
											<div class="d-flex align-items-center row">

												<div class="pd-2 col-lg-3  col-md-12 col-sm-12 col-xs-12">
													<select class="custom-select tx-center" v-model="$app_estimate.lib_estimate.rental_car_estimate[index].rental_date">
														<option value="">이용날짜를 선택하세요.</option>
														<option v-for="(code, idx) in $app_estimate.dateRange" :value="code">{{ code }}</option>
													</select>
													<!-- <input type="date" class="form-control" placeholder="이용날짜 입력" v-model="$app_estimate.lib_estimate.rental_car_estimate[index].rental_date" :min="search.sel_exp_ci_date" :max="search.sel_exp_co_date"> -->
												</div>

												<div class="pd-2 col-lg-3  col-md-12 col-sm-12 col-xs-12">
													<select class="custom-select" @change="$app_estimate.setCarSelectValue(index)" v-model="$app_estimate.lib_estimate.rental_car_estimate[index].car_no">
														<option value="0" selected>차량을 선택해주세요.</option>
														<!-- <option v-for="(code, idx) in rental_car_list" :value="code.no">{{ code.name }}-{{ code.cost_per_night }}페소</option> -->
														<option v-for="(code, idx) in rental_car_list" :value="code.no">{{ code.name }}</option>
													</select>
												</div>
												<div class="pd-2 col-lg-2  col-md-12 col-sm-12 col-xs-12">
													<div class="input-group">
														<select class="custom-select tx-center" @change="$app_estimate.setCarSelectValue(index)" v-model="$app_estimate.lib_estimate.rental_car_estimate[index].rental_counts">
															<option v-for="number in 10" :value="number">{{ number }}대</option>
														</select>
													</div>
												</div>

												<div class="pd-2 col-lg-3 col-md-12 col-sm-12 col-xs-12">
													<div class="input-group">
														<span class="form-control text-right">{{ $common.formatMoney($app_estimate.lib_estimate.rental_car_estimate[index].rental_amount) }}</span>
														<div class="input-group-append">
															<span class="input-group-text">페소</span>
														</div>
													</div>
													<!-- <small v-if="index == ( $app_estimate.lib_estimate.rental_car_estimate.length -1 )" class="form-text text-danger"> * 기사포함 및 주유비/팁 별도</small> -->
												</div>
												<div class="pd-2 col-lg-1 col-md-12 col-sm-12 col-xs-12 text-center">
													<a class="btn btn-xs btn-outline-danger btn-icon" href="javascript:void(0);" @click="$app_estimate.deleteRow($app_estimate.lib_estimate.rental_car_estimate,index)">삭제하기</a>
												</div>
											</div>
										</td>
									</tr>
								</template>
							</tbody>
							<tfoot class="tfoot-estmate">
								<tr align="center">
									<th colspan="1">차량 렌트 총 비용</th>
									<th colspan="12" class="">
										<div class="text-right"><!-- right -->
											<div class="font-size-sm pt-2 "><span class="text-muted mr-2">{{$common.formatMoney($app_estimate.exchange_kor($app_estimate.lib_estimate.rental_car_amount))}} 원</span></div>
											<h5 class="product-card-title font-weight-bold border-0 pt-0 pd-10 mb-0">{{$common.formatMoney($app_estimate.lib_estimate.rental_car_amount)}} 페소 </h5>
										</div>
										<!-- {{$common.formatMoney(rental_car_amount)}} 페소< -->
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<!-- list area end -->
		</div>
	</div>
	<!-- !차량견적 -->

	<!-- 골프견적 -->
	<div class="mg-b-0 d-flex flex-row justify-content-between">
		<div class="align-self-center">
			<h5 class="mg-t-10" style="font-family:'NanumSquareNeo BD'"> 골프견적 </h5>
		</div>
		<div class="align-self-center"><a class="btn btn-sm btn-outline-primary" href="javascript:void(0);" @click="$app_estimate.addRow_golf"> <i data-feather="plus" class="mg-r-5"></i>골프견적추가</a></div>
	</div>
	<div class="row mt-2">
		<!-- list area start -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="media">
				<div class="media-body">
					<table class="table mg-b-0">
						<thead hidden class="thead-estmate">
							<tr align="center">
								<th scope="col" width="">구분</th>
								<th scope="col" width="20%">인원</th>
								<th scope="col" width="20%">비용</th>
								<th scope="col" width="10%"></th>
							</tr>
						</thead>
						<tbody>
							<template v-if="$app_estimate.lib_estimate.golf_estimate.length < 1">
								<tr>
									<td colspan="13" class="pd-l-0 pd-r-0">
										<div class="alert alert-info" role="alert"> 골프요금을 추가해주세요. </div>
									</td>
								</tr>
							</template>
							<template v-else>
								<tr v-for="(golf, index) in $app_estimate.lib_estimate.golf_estimate" :key="index">
									<td colspan="13">
										
										<div class="d-flex align-items-center row">
											<!-- 이용날짜 -->
											<div class="pd-2 col-lg-3 col-md-12 col-sm-12 col-xs-12">
												<!-- <input type="date" class="form-control" placeholder="이용날짜 입력" v-model="$app_estimate.lib_estimate.golf_estimate[index].golf_date" :min="search.sel_exp_ci_date" :max="search.sel_exp_co_date" @change="$app_estimate.resetGolfClub(index)"> -->
												<select class="custom-select tx-center" @change="$app_estimate.resetGolfClub(index)" v-model="$app_estimate.lib_estimate.golf_estimate[index].golf_date">
													<option value="">이용날짜를 선택하세요.</option>
													<option v-for="(code, idx) in $app_estimate.dateRange" :value="code">{{ code }}</option>
												</select>
											</div>
											<!-- 골프장을 선택 -->
											<div class="pd-2 col-lg-3 col-md-12 col-sm-12 col-xs-12">
												<select class="custom-select" @change="$app_estimate.setGolfSelectValue(index,$app_estimate.checkDayOfWeek_friday(index))" v-model="$app_estimate.lib_estimate.golf_estimate[index].golf_no">
													<template v-if="$app_estimate.lib_estimate.golf_estimate[index].golf_date == '' ">
														<option value="0" selected>골프 날짜을 선택해주세요.</option>
													</template>
													<template v-else>
														<option value="0" selected>골프장을 선택해주세요.</option>
														<option v-for="(code, idx) in $app_estimate.filteredGolfClubList(index)" :value="code.no">{{ code.name }}</option>
													</template>
												</select>
											</div>
											<!-- 인원 -->
											<div class="pd-2 col-lg-2  col-md-12 col-sm-12 col-xs-12">
												<div class="input-group">
													<select class="custom-select tx-center" @change="$app_estimate.setGolfSelectValue(index,$app_estimate.checkDayOfWeek_friday(index))" v-model="$app_estimate.lib_estimate.golf_estimate[index].golf_people">
														<option v-for="number in 10" :value="number">{{ number }}인</option>
													</select>
												</div>
											</div>
											<div class="pd-2 col-lg-3  col-md-12 col-sm-12 col-xs-12">
												<div class="input-group">
													<span class="form-control text-right">{{ $common.formatMoney(golf.golf_amount )}}</span>
													<div class="input-group-append">
														<span class="input-group-text">페소</span>
													</div>
												</div>
											</div>
											<div class="pd-2 col-lg-1 col-md-12 col-sm-12 col-xs-12 text-center">
												<a class="btn btn-xs btn-outline-danger btn-icon" href="javascript:void(0);" @click="$app_estimate.deleteRow($app_estimate.lib_estimate.golf_estimate,index)">삭제하기</a>
											</div>
										</div>
									</td>
								</tr>
							</template>
						</tbody>
						<tfoot class="tfoot-estmate">
							<tr align="center">
								<th>골프 총 비용</th>
								<th colspan="12" class="">
									<div class="text-right"> <!-- right -->
										<div class="font-size-sm pt-2 "><span class="text-muted mr-2">{{$common.formatMoney($app_estimate.exchange_kor($app_estimate.lib_estimate.golf_amount))}} 원</span></div>
										<h5 class="product-card-title font-weight-bold border-0 pt-0 pd-10 mb-0">{{$common.formatMoney($app_estimate.lib_estimate.golf_amount)}} 페소 </h5>
									</div>
									<!-- {{$common.formatMoney(rental_car_amount)}} 페소< -->
								</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<!-- list area end -->
	</div>
	<!-- !골프견적 -->
	<!-- total amount area start -->
	<!-- <hr class="mg-t-0"> -->
	<table class="table mg-b-40 mg-t-40">
		<tfoot class="tfoot-estmate">
			<tr align="center">
				<th>
					<h5>총 여행비용</h5>
				</th>
				<th colspan="12" class="">
					<div class="text-right"> <!-- right -->
						<div class="font-size-sm"><span class="text-muted mr-3">{{$common.formatMoney($app_estimate.exchange_kor($app_estimate.lib_estimate.total_amount))}} 원</span></div>
						<h4 class="product-card-title font-weight-bold border-0 pt-0 pb-0 mb-0 mr-3">{{$common.formatMoney($app_estimate.lib_estimate.total_amount)}} 페소 </h4>
					</div>
					<!-- {{$common.formatMoney(rental_car_amount)}} 페소< -->
				</th>
			</tr>

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
	<hr class="mg-t-40 mg-b-20">
	<a class="btn btn-secondary" @click="$common.back_to_list()" href="javascript:void()">돌아가기</a>
	<button v-if="action_type == 'update' " href="#modal_delete" data-toggle="modal" class="btn btn-danger ">삭제하기</button>
	<button href="#modal_save" data-toggle="modal" class="btn btn-success">저장하기</button>
	<!-- container -->

	<!-- content -->


	<!-- modal start -->
	<div class="modal fade" id="modal_save" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content tx-14">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">AutoInvoice System</h6>
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
					<h6 class="modal-title" id="exampleModalLabel2">AutoInvoice system</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p class="mg-b-0">삭제하시겠습니까?</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-danger tx-13" @click="delete_estimate()">삭제하기</button>
				</div>
			</div>
		</div>
	</div>


	<!-- select room Modal -->
	<div class="modal fade" id="roomSelectModal" tabindex="-1" role="dialog" aria-labelledby="roomSelectModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title" id="exampleModalLabel2">AutoInvoice system</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body-sm">
					<!-- search area start-->
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
									<label for="commoncode_1_select" class="d-block">풀빌라</label>
									<select class="custom-select tx-center" id="commoncode_1_select" v-model="search.sel_rooms">
										<option value="1">1채</option>
										<option value="2">2채</option>
										<option value="3">3채</option>
										<option value="4">4채</option>
										<option value="5">5채</option>
									</select>
								</div>
								<!-- 인원 -->
								<div class="pd-2 flex-fill ">

									<label for="commoncode_2_select" class="d-block">인원</label>
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

					<template v-if="searched_rooms.count_total < 1">
						<div v-if="is_room_searched" class="alert alert-danger " role="alert">
							해당 조건으로 예약 가능한 객실이 없습니다.
						</div>
						<div v-else class="alert alert-info	" role="alert">
							원하시는 여행 일정을 검색해주세요.
						</div>
					</template>
					<template v-else>

						<div class="table-responsive">

							<table id="ab_room_list" class="table table-sm table table-info align-self-center" width="100%">
								<thead>
									<!-- <tr align="center"> -->
									<!-- <th width="5%">#</th> -->
									<!-- <th width="">객실정보</th> -->
									<!-- 	<th width="">객실</th>
											<th width="">투숙일정</th>
											<th width="">총요금</th> -->
									<!-- <th width=""></th> -->
									<!-- </tr> -->
								</thead>

								<tbody>
									<tr v-for="(item, idx) in searched_rooms.result_ab_room" :class="[(checkedRes.includes(item.room_type_no) ? 'active_row' : ''), $app_estimate.getColor(item) ]" :style="{ 'background-color': $app_estimate.getColor_background(item) }">
										<!-- 	<td align="center">{{ idx + 1 }}</td> -->
										<td width="100%" align="left">
											<div class="row">
												<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 d-flex">
													<div class="flex-fill">[{{ item.type_name }}]</div>

												</div>
												<div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 d-flex">
													<div class="flex-fill">{{ item.check_in }}-{{ item.check_out }}({{ item.req_nights }}박)/{{ item.req_rooms }}채/{{ search.sel_guests }}인</div>

												</div>
												<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 d-flex">
													<div class="flex-fill">{{$common.formatMoney(item.charge_amount_total)}} 페소({{$common.formatMoney(item.charge_amount_total_kr)}}원)</div>
												</div>
											</div>
											<!-- <div class="row">
												
												<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex">
													<div class="pd-10 bg-gray-300 flex-fill">[{{ item.type_name }}]</div>
													<div class="pd-10 bg-gray-300 flex-fill">{{ item.check_in }}-{{ item.check_out }}({{ item.req_nights }}박)</div>
													<div class="pd-10 bg-gray-300 flex-fill">{{ item.req_rooms }}채</div>
													<div class="pd-10 bg-gray-300 flex-fill">{{$common.formatMoney(item.charge_amount_total)}} 페소({{$common.formatMoney(item.charge_amount_total_kr)}}원)</div>
												</div>
											</div> -->
										</td>



										<td class="pd-l-0" style="line-height: 3.5;" align="center">

											<div v-if="item.available_rooms === 0" class="btn-group align-top">
												<button type="button" data-toggle="modal" data-target="#user-form-modal" class="btn btn-danger badge">예약불가</button>
											</div>
											<div v-else class="custom-control custom-checkbox">
												<input hidden type="checkbox" :id="'customCheck' + idx" :value="item.room_type_no" v-model="checkedRes" @change="$app_estimate.selectRoom">
												<label class="btn btn-xs btn-primary mg-b-0" :for="'customCheck' + idx" style="min-width: 60px;">{{ checkedRes.includes(item.room_type_no) ? '선택 해제' : '선택' }}</label>
											</div>
										</td>
									</tr>

								</tbody>
							</table>
						</div>
						<!-- search area end -->
						<!-- 	<img v-if="selectedImage" :src="selectedImage.source" class="img-fluid" :alt="selectedImage.alt"> -->

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
	//$.getScript("/assets/js/unyict/common-lib_v1_6.js");
	$.getScript("/assets/js/unyict/estimate-lib.js");
	$(document).ready(function() {
		$.getScript("<?php echo element('view_skin_url', $layout); ?>js/write.js");
	});
</script>