<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class Upload_ckeditor extends Controller
{
    public function index()
    {
        try {
            // 현재 날짜를 기준으로 폴더 경로 생성
            $year = date('Y'); // 연도
            $month = date('m'); // 월
            $day = date('d'); // 일

            $fileRoute = config('App')->uploadsDir . '/ck_editor/' . $year . '/' . $month . '/' . $day . '/';
            log_message('error', "Upload path for image {$fileRoute}");

            // 경로가 존재하지 않을 경우 생성
            if (!is_dir($fileRoute)) {
                if (!mkdir($fileRoute, 0777, true)) {
                    log_message('error', "Failed to create directory: " . $fileRoute);
                    throw new \Exception("Failed to create directory: " . $fileRoute);
                }
            }

            $fieldname = "upload";  // 변경된 필드명

            // 파일 업로드 에러 체크
            if (!isset($_FILES[$fieldname])) {
                throw new \Exception("No file uploaded.");
            }

            if ($_FILES[$fieldname]['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("File upload error. Error code: " . $_FILES[$fieldname]['error']);
            }

            // 파일명 가져오기
            $filename = explode(".", $_FILES[$fieldname]["name"]);

            // 업로드된 파일 검증
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if (!$finfo) {
                throw new \Exception("Failed to open fileinfo.");
            }

            $tmpName = $_FILES[$fieldname]["tmp_name"];
            if (!$tmpName) {
                throw new \Exception("No temporary file name.");
            }

            $mimeType = finfo_file($finfo, $tmpName);
            if (!$mimeType) {
                throw new \Exception("Failed to get mime type.");
            }

            $extension = end($filename);

            // 허용된 확장자
            $allowedExts = array("txt", "pdf", "doc", "json", "html", "jpg", "jpeg", "png", "gif", "ico");

            // 허용된 mime 타입
            $allowedMimeTypes = array("image/jpeg", "image/png", "text/plain", "application/msword", "application/x-pdf", "application/pdf", "application/json", "text/html");

            // 파일 검증
            if (!in_array(strtolower($mimeType), $allowedMimeTypes) || !in_array(strtolower($extension), $allowedExts)) {
                log_message('error', "File does not meet the validation: MIME Type - {$mimeType}, Extension - {$extension}");
                throw new \Exception("File does not meet the validation.");
            }

            // 새로운 파일명 생성
            $name = sha1(microtime()) . "." . $extension;
            $fullNamePath =  $fileRoute . $name;
            log_message('error', "Image fullNamePath : " . $fullNamePath);

            // 서버 프로토콜 확인 및 리소스 로드
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") {
                $protocol = "https://";
            } else {
                $protocol = "http://";
            }

            // 파일을 업로드 폴더에 저장
            if (!move_uploaded_file($tmpName, $fullNamePath)) {
                throw new \Exception("Failed to move uploaded file.");
                log_message('error', "Failed to move uploaded file.");
            }
            log_message('error', "uploaded file : " . $fullNamePath);

            // 이미지 최적화 함수 호출
            $optimized_imageName = $this->optimize_image($fullNamePath);
            if (!$optimized_imageName) {
                log_message('error', "Failed to optimize image ");                
            }

            log_message('error', "Optimized image name for image {$optimized_imageName}");

            // 결과 반환
            $response = array(
                'uploaded' => true,
                'fileName' => $optimized_imageName,
                'url' => '/'.config('App')->uploadsDir.'/ck_editor/' . $year . '/' . $month . '/' . $day . '/' . $name,
                'error' => null
            );
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            // 에러 응답 전송
            $response = array(
                'uploaded' => false,
                'fileName' => '',
                'url' => '',
                'error' => $e->getMessage()
            );
            return $this->response->setJSON($response)->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }
    }
    function optimize_image($imagePath)
	{
		// 이미지 정보를 가져옴
		$imageInfo = getimagesize($imagePath);
		if ($imageInfo === false) {
			return false; // 이미지 파일이 아니면 처리 중단
		}

		$width = $imageInfo[0];
		$height = $imageInfo[1];
		$type = $imageInfo[2];

		// 이미지 타입에 따라 이미지 생성
		switch ($type) {
			case IMAGETYPE_JPEG:
				$source = imagecreatefromjpeg($imagePath);
				break;
			case IMAGETYPE_PNG:
				$source = imagecreatefrompng($imagePath);
				break;
			case IMAGETYPE_GIF:
				$source = imagecreatefromgif($imagePath);
				break;
			default:
				return false; // 지원하지 않는 이미지 타입
		}

		// 최적화할 이미지의 경로 (예: 원본 경로에 '_optimized' 추가)
		$optimizedPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '_optimized.$1', $imagePath);

		// 이미지 품질 조정 및 저장
		// JPEG 이미지의 경우 품질을 75(0-100 사이)로 설정
		// PNG 이미지의 경우 압축 수준을 6(0-9 사이)로 설정 (0은 압축 없음, 9는 최대 압축)
		if ($type === IMAGETYPE_JPEG) {
			imagejpeg($source, $optimizedPath, 75);
		} elseif ($type === IMAGETYPE_PNG) {
			imagepng($source, $optimizedPath, 6);
		} elseif ($type === IMAGETYPE_GIF) {
			imagegif($source, $optimizedPath);
		}

		//사진 가로 사이즈 조정 
		//320px 이하인 경우 그대로 사용
		//320px 초과인 경우 320px로 조정
		/* 		if ($width > 320) {
			$source = imagecreatefromjpeg($optimizedPath);
			$width = 320;
			$height = $height * (320 / $imageInfo[0]);
			$destination = imagecreatetruecolor($width, $height);
			imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $imageInfo[0], $imageInfo[1]);
			imagejpeg($destination, $optimizedPath, 75);
		}
 */

		//사진 세로 사이즈 조정
		//1080px 이하인 경우 그대로 사용
		//1080px 초과인 경우 1080px로 조정
		if ($height > 1080) {
			$source = imagecreatefromjpeg($optimizedPath);
			$height = 1080;
			$width = $width * (1080 / $imageInfo[1]);
			$destination = imagecreatetruecolor($width, $height);
			imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $imageInfo[0], $imageInfo[1]);
			imagejpeg($destination, $optimizedPath, 75);
		}

		// 메모리 정리
		imagedestroy($source);

		return $optimizedPath;
	}
}
