<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class EstimateApi extends ResourceController
{
    public function __invoke($method)
    {
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    public function index()
    {
        return $this->respond([
            'status' => '200',
            'message' => 'Estimate API index method'
        ]);
    }

    public function getTest()
    {
        return $this->respond([
            'status' => '200',
            'message' => 'Estimate API getTest method'
        ]);
    }

    public function getEstimateList()
    {
        // 클라이언트로부터 POST 데이터를 가져옵니다.
        $postData = $this->request->getJSON(); // JSON 데이터 가져오기

        // 필터링에 사용할 날짜와 기타 매개변수를 설정합니다.
        $sdate = $postData['sdate'] ?? '2024-08-01';
        $edate = $postData['edate'] ?? '2024-08-03';
        $guestName = $postData['guest_name'] ?? '';
        $status = $postData['status'] ?? '';

        // DataTables가 전송하는 매개변수를 설정합니다.
        $draw = $postData['draw'];
        $start = $postData['start'];
        $length = $postData['length'];

        // 필터링 조건을 설정합니다.
        $where = [
            'insert_date_ymd >=' => $sdate,
            'insert_date_ymd <=' => $edate,
            'is_deleted !=' => 1,
            'is_temp !=' => 1,
            'type !=' => 'order',
        ];

        // 고객 이름으로 필터링하는 조건을 추가합니다.
        if (!empty($guestName)) {
            $where['guest_name like'] = '%' . $guestName . '%';
        }

        // 모델을 사용하여 데이터베이스와 상호작용합니다.
        $model = new \App\Models\EstimateModel();

        // 전체 레코드 수를 계산합니다.
        $totalRecords = $model->db->table('v_estimate')->countAllResults();

        // 필터링된 레코드 수를 계산합니다.
        $totalFiltered = $model->countFilteredResults('v_estimate', $where);

        // 요청된 페이지에 해당하는 데이터를 가져옵니다.
        $data = $model->getAdminListDt('v_estimate', $where, 'no', 'desc', $start, $length);

        // 로그 추가
        log_message('debug', 'Filtered Data: ' . json_encode($data));

        // 데이터가 있는 경우 관련 데이터를 미리 로드하고 매핑합니다.
        if (!empty($data['total_rows'])) {
            $estimateNos = array_column($data['list'], 'no');
            $whereEstimate = [
                'estimate_no' => $estimateNos,
                'is_deleted <>' => 1,
            ];

            // 관련 데이터를 미리 로드합니다.
            $rooms = $model->getAdminListDtAll('v_reservation', $whereEstimate);
            $rentalCars = $model->getAdminListDtAll('v_estimate_car', $whereEstimate);
            $golfs = $model->getAdminListDtAll('v_estimate_golf', $whereEstimate);
            $itemEstimates = $model->getAdminListDtAll('v_estimate_item', $whereEstimate);

            // 관련 데이터를 각 견적에 매핑합니다.
            foreach ($data['list'] as $key => $value) {
                $estimateNo = $value['no'];
                $data['list'][$key]['room'] = array_values(array_filter($rooms['list'], fn($item) => $item['estimate_no'] == $estimateNo));
                $data['list'][$key]['rental_car'] = array_values(array_filter($rentalCars['list'], fn($item) => $item['estimate_no'] == $estimateNo));
                $data['list'][$key]['golf'] = array_values(array_filter($golfs['list'], fn($item) => $item['estimate_no'] == $estimateNo));
                $data['list'][$key]['item_estimate'] = array_values(array_filter($itemEstimates['list'], fn($item) => $item['estimate_no'] == $estimateNo));

                // 각 견적 항목의 세부 정보를 추가로 로드합니다.
                foreach ($data['list'][$key]['item_estimate'] as $keyItemEstimate => $itemEstimateValue) {
                    $itemEstimateDetail = $model->getAdminListDt(
                        'v_estimate_item_detail',
                        ['estimate_item_no' => $itemEstimateValue['no']]
                    );
                    if (!empty($itemEstimateDetail['list'])) {
                        $data['list'][$key]['item_estimate'][$keyItemEstimate]['estimate_item_detail'] = $itemEstimateDetail['list'];
                    }
                }
            }
        }

        // DataTables가 요구하는 형식으로 응답을 반환합니다.
        return $this->respond([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data['list'] // 'data' 키를 사용하여 반환합니다.
        ]);
    }
}