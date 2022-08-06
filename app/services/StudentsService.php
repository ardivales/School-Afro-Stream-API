<?php

namespace App\Services;

use App\Models\Students;

class StudentsService {

	const ERROR_EMAIL_ALREADY_USED = 001;

	const ERROR_UNABLE_CREATE_STUDENT = 002;

	/**
	 * @param array $data
	 *
	 * @return array
	 *
	 * Add Student to data base
	 */
	public function add(array $data)
	{
		try {
			$verify_student_email_exist = Students::findFirst([
					"conditions" => "email = :email:",
					"bind" => [
						'email' => $data['email'],
					]
				]
			);

			if ($verify_student_email_exist) {
				throw new ServiceException("cet email est déja utilisé par un autre étudiant.", self::ERROR_EMAIL_ALREADY_USED);
			}

			$student = new Students();

			$create_student = $student
				->setClasse($data["classe"])
				->setAge($data["age"])
				->setAddress($data["address"])
				->setEmail($data["email"])
				->setName($data['name'])
				->create();

			if (!$create_student) {
				throw new ServiceException("Impossible de créer l'étudiant.", self::ERROR_UNABLE_CREATE_STUDENT);
			}

			$output = [];
			$output["status"] = "success";
			$output["message"] = "Etudiant crée avec succès.";
			$output["student_info"] = $this->get_object_to_array($student);
			return $output;
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), 10001, $e);
		}
	}

	public function get_object_to_array($student)
	{
		$student_info = array();
		if (isset($student) && !empty($student)) {
			$data = Students::findFirst($student->getId());
			if (isset($data) && !empty($data)) {
				$student_info["student_id"] = $data->getId();
				$student_info["name"] = $data->getName();
				$student_info["email"] = $data->getEmail();
				$student_info["address"] = $data->getAddress();
				$student_info["age"] = $data->getAge();
				$student_info["classe"] = $data->getClasse();
			}
		}
		return $student_info;
	}
}