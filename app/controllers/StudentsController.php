<?php

namespace App\Controllers;

use App\Services\ServiceException;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;

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

}