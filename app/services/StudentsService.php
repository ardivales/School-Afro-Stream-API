<?php

namespace App\Services;

use App\Models\Students;

class StudentsService {

	const ERROR_EMAIL_ALREADY_USED = 001;

	const ERROR_UNABLE_CREATE_STUDENT = 002;

	const ERROR_STUDENT_NOT_FOUND = 003;

	const ERROR_UNABLE_UPDATE_STUDENT = 004;

	const ERROR_DELETED_ACCOUNT = 005;

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

	/**
	 * Update STUDENT.
	 *
	 * @param array $data The STUDENT data.
	 * @return array $output The output.
	 * @throws ServiceException The service exception.
	 */
	public function update(array $data)
	{
		try {
			$student = Students::findFirst([
				"conditions" => "id=:student_id:",
				"bind" => [
					'student_id' => $data['student_id']
				]
			]);

			if (!$student) {
				throw new ServiceException('Impossible de trouver cet étudiant.', self::ERROR_STUDENT_NOT_FOUND);
			}

			$student_find_by_email = Students::findFirst([
					"conditions" => "email = :email: AND id != :id:",
					"bind" => [
						'email' => $data['email'],
						'id' => $data['student_id']
					]
				]
			);

			if ($student_find_by_email) {
				throw new ServiceException("Cet email est déja utilisé pour un autre étudiant.", self::ERROR_EMAIL_ALREADY_USED);
			}

			$data['name'] = (is_null($data['name'])) ? $student->getName() : $data['name'];
			$data['email'] = (is_null($data['email'])) ? $student->getEmail() : $data['email'];
			$data['age'] = (is_null($data['age'])) ? $student->getAge() : $data['age'];
			$data['address'] = (is_null($data['address'])) ? $student->getAddress() : $data['address'];
			$data['classe'] = (is_null($data['classe'])) ? $student->getClasse() : $data['classe'];

			$result = $student->setUpdatedAt(date('Y-m-d H:i:s'))
			                  ->setClasse($data["classe"])
			                  ->setAge($data["age"])
			                  ->setAddress($data["address"])
			                  ->setEmail($data["email"])
			                  ->setName($data['name'])
			                  ->update();

			if (!$result) {
				throw new ServiceException("Impossible de mettre à jour l'étudiant.", self::ERROR_UNABLE_UPDATE_STUDENT);
			} else {
				$output = [];
				$output["status"] = "success";
				$output["message"] = "Le compte de l'étudiant à bien été mise a jour.";
				$output['student_info'] = $this->get_object_to_array($student);
				return $output;
			}
		} catch (\PDOException $e) {
			throw new ServiceException($e->getMessage(), 10001, $e);
		}
	}

	/**
	 * Delete STUDENT.
	 *
	 * @param array $data The STUDENT data.
	 * @return array $output The output.
	 * @throws ServiceException The service exception.
	 */
	public function delete(array $data)
	{
		$student = Students::findFirst([
			'conditions' => 'id=:id:',
			'bind' => [
				'id' => $data['student_id']
			]
		]);

		if (!$student) {
			throw new ServiceException("L'étudiant est introuvable.", self::ERROR_STUDENT_NOT_FOUND);
		}

		$result = $student->delete();

		if (!$result) {
			throw new ServiceException("Impossible de supprimer l'étudiant.", self::ERROR_DELETED_ACCOUNT);
		} else {
			$output = [];
			$output["status"] = "success";
			$output["message"] = "Etudiant supprimé avec succès.";
			return $output;
		}
	}

	/**
	 * Get Student By ID
	 * @param array $data
	 *
	 * @return array
	 */
	public function get_by_id(array $data)
	{
		$student = Students::findFirst([
			'conditions' => 'id=:id:',
			'bind' => [
				'id' => $data['student_id']
			]
		]);

		if (!$student) {
			throw new ServiceException("L'étudiant est introuvable.", self::ERROR_STUDENT_NOT_FOUND);
		}
		$output = [];
		$output["status"] = "success";
		$output["message"] = "Information de l'étudiant.";
		$output["student_info"] = $this->get_object_to_array($student);
		return $output;
	}

	/**
	 * GET ALL STUDENTS
	 * @return array
	 *
	 */
	public function get_all()
	{
		$students_info = [];
		$students = Students::find();
		if (!empty($students)) {
			foreach ($students as $student) {
				$students_info[] = $this->get_object_to_array($student);
			}
		}
		$output = [];
		$output["status"] = "success";
		$output["message"] = "Liste des étudiants.";
		$output["students_info"] = $students_info;
		return $output;
	}

	/**
	 * @param $student
	 *
	 * @return array
	 */
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