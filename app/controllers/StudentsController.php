<?php

namespace App\Controllers;

use App\Services\ServiceException;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;

class StudentsController extends AbstractController {

	/**
	 * Create Students Validator
	 *
	 * @return Validation $validator The validator.
	 */
	public function set_validator_add($data)
	{
		$validator = new Validation();
		$validator->add([
			"name",
			"email",
			"classe",
			"age"
		], new PresenceOf(
				["message" => [
					"name" => "Le nom de l'étudiant est requis.",
					"email" => "L'email de l'étudiant est requis.",
					"classe" => "La classe de l'étudiant est requis.",
					"age" => "L'age de l'étudiant est requis."
				]
				]
			)
		);

		$validator->add(
			[
				"email"
			], new Email(
				[
					"message" => ["email" => "Le champ email est invalide.",
					]
				]
			)
		);
		return $validator;
	}

	/**
	 * Update Student validator.
	 *
	 * @return Validation $validator The validator.
	 */
	public function set_validator_update($data)
	{
		$validator = new Validation();
		$validator->add([
			"student_id",
			"name",
			"email",
			"classe",
			"age"
		], new PresenceOf(
				["message" => [
					"student_id" => "Le champ identifiant de l'étudiant est requis.",
					"name" => "Le nom de l'étudiant est requis.",
					"email" => "L'email de l'étudiant est requis.",
					"classe" => "La classe de l'étudiant est requis.",
					"age" => "L'age de l'étudiant est requis."
				]
				]
			)
		);

		$validator->add(
			[
				"student_id"
			], new Numericality(
				[
					"message" => [
						"student_id" => "Le champ identifiant de l'étudiant n'est pas numérique.",
					]
				]
			)
		);

		$validator->add(
			[
				"email"
			], new Email(
				[
					"message" => ["email" => "Le champ email est invalide.",
					]
				]
			)
		);
		return $validator;
	}

	/**
	 * Delete Student validator.
	 *
	 * @return Delete $validator The validator.
	 */
	public function set_validator_delete()
	{
		$validator = new Validation();
		$validator->add([
			"student_id",
		], new PresenceOf([
					"message" => [
						"student_id" => "Le champ identifiant de l'étudiant est requis.",
					]
				]
			)
		);

		$validator->add(
			[
				"student_id"
			], new Numericality(
				[
					"message" => [
						"student_id" => "Le champ identifiant de l'étudiant n'est pas numérique.",
					]
				]
			)
		);

		return $validator;
	}

	/**
	 * Get Student by ID
	 *
	 * @return Validation
	 *
	 */
	public function set_validator_get_by_id()
	{
		$validator = new Validation();
		$validator->add([
			"student_id",
		], new PresenceOf([
					"message" => [
						"student_id" => "Le champ identifiant du l'étudiant est requis.",
					]
				]
			)
		);

		$validator->add(
			[
				"student_id"
			], new Numericality(
				[
					"message" => [
						"student_id" => "Le champ identifiant du l'étudiant n'est pas numérique.",
					]
				]
			)
		);

		return $validator;
	}

	/**
	 * CREATE USER ACTION.
	 *
	 * @param type $json_data The json data.
	 * @return type $output The output.
	 */
	public function add_action($json_data = null)
	{
		//Init Block.
		$data = $this->get_data($this->request->getJsonRawBody(), $json_data, array("address"));

		//Validation Block.
		$this->validate($this->set_validator_add($data), $data);

		//Passing to business logic and preparing the response.
		try {
			$output = $this->students_service->add($data);
			return $output;
		} catch (ServiceException $e) {
			$this->handle_exceptions($e);
		}
	}

	/**
	 * Update Student action.
	 *
	 * @param type $json_data The json data.
	 * @return type $output The output.
	 */
	public function update_action($json_data = null)
	{
		//Init Block.
		$data = $this->get_data($this->request->getJsonRawBody(), $json_data, array("address"));

		//Validation Block.
		$this->validate($this->set_validator_update($data), $data);

		//Passing to business logic and preparing the response.
		try {
			$output = $this->students_service->update($data);
			return $output;
		} catch (ServiceException $e) {
			$this->handle_exceptions($e);
		}
	}

	/**
	 * Enable disable action.
	 *
	 * @param type $json_data The json data.
	 * @return type $output The output.
	 */
	public function delete_action($student_id)
	{
		//Init Block.
		$data = ["student_id" => $student_id];

		//Validation Block.
		$this->validate($this->set_validator_delete(), $data);

		//Passing to business logic and preparing the response.
		try {
			$output = $this->students_service->delete($data);
			return $output;
		} catch (ServiceException $e) {
			$this->handle_exceptions($e);
		}
	}

	public function get_by_id_action($student_id)
	{
		//Init Block.
		$data = ["student_id" => $student_id];

		//Validation Block.
		$this->validate($this->set_validator_get_by_id(), $data);

		//Passing to business logic and preparing the response.
		try {
			$output = $this->students_service->get_by_id($data);
			return $output;
		} catch (ServiceException $e) {
			$this->handle_exceptions($e);
		}
	}

}