<?php

class user extends common {

	public static $actions = [
		'add' => self::RANK_ADMIN,
		'delete' => self::RANK_ADMIN,
		'edit' => self::RANK_MEMBER,
		'index' => self::RANK_ADMIN,
		'login' => self::RANK_VISITOR,
		'logout' => self::RANK_MEMBER
	];
	public static $ranks = [
		self::RANK_BANNED => 'Banni',
		self::RANK_MEMBER => 'Membre',
		self::RANK_MODERATOR => 'Modérateur',
		self::RANK_ADMIN => 'Administrateur'
	];
	public static $users = [];

	/**
	 * Ajout
	 */
	public function add() {
		// Soumission du formulaire
		if($this->isPost()) {
			// Double vérification pour le mot de passe
			$password = $this->getInput('userAddPassword', helper::FILTER_PASSWORD);
			// La confirmation ne correspond pas au mot de passe
			if($password !== $this->getInput('userAddConfirmPassword', helper::FILTER_PASSWORD)) {
				template::$notices['userAddConfirmPassword'] = 'La confirmation ne correspond pas au mot de passe';
			}
			// Crée l'utilisateur
			$this->setData([
				'user',
				helper::increment($this->getInput('userAddName', helper::FILTER_URL), $this->getData(['user'])),
				[
					'name' => $this->getInput('userAddName'),
					'rank' => $this->getInput('userAddRank', helper::FILTER_INT),
					'password' => $password
				]
			]);
			return [
				'redirect' => 'user',
				'notification' => 'Utilisateur créé',
				'state' => true
			];
		}
		// Affichage du template
		else {
			return [
				'title' => $this->getData(['user', $this->getUrl(2), 'name']),
				'view' => true
			];
		}
	}

	/**
	 * Suppression
	 */
	public function delete() {
		// Accès refusé
		if(
			// L'utilisateur n'existe pas
			$this->getData(['user', $this->getUrl(2)]) === null
			// Rang insuffisant
			AND ($this->getUrl('rank') < self::RANK_MODERATOR)
		) {
			return [
				'access' => false
			];
		}
		// Bloque la suppression de son propre compte
		if($this->getUser('id') === $this->getUrl(2)) {
			return [
				'redirect' => 'user',
				'notification' => 'Impossible de supprimer votre propre compte'
			];
		}
		// Suppression
		else {
			$this->deleteData(['user', $this->getUrl(2)]);
			return [
				'redirect' => 'user',
				'notification' => 'Utilisateur supprimé',
				'state' => true
			];
		}
	}

	/**
	 * Édition
	 */
	public function edit() {
		// Accès refusé
		if(
			// L'utilisateur n'existe pas
			$this->getData(['user', $this->getUrl(2)]) === null
			// Droit d'édition
			AND (
				// Impossible de s'auto-éditer
				(
					$this->getUser('id') === $this->getUrl(2)
					AND $this->getUrl('rank') <= self::RANK_VISITOR
				)
				// Impossible d'éditer un autre utilisateur
				OR ($this->getUrl('rank') < self::RANK_MODERATOR)
			)
		) {
			return [
				'access' => false
			];
		}
		// Soumission du formulaire
		elseif($this->isPost()) {
			// Double vérification pour le mot de passe
			if($this->getInput('userEditNewPassword')) {
				$newPassword = $this->getInput('userEditNewPassword', helper::FILTER_PASSWORD);
				// Pas de changement de mot de passe en mode démo
				if(self::$demo) {
					$newPassword = $this->getData(['user', $this->getUrl(2), 'password']);
					template::$notices['userEditNewPassword'] = 'Action impossible en mode démonstration !';
				}
				// La confirmation ne correspond pas au mot de passe
				elseif($newPassword !== $this->getInput('userEditConfirmPassword', helper::FILTER_PASSWORD)) {
					$newPassword = $this->getData(['user', $this->getUrl(2), 'password']);
					template::$notices['userEditConfirmPassword'] = 'La confirmation ne correspond pas au mot de passe';
				}
			}
			// Sinon conserve le mot de passe d'origine
			else {
				$newPassword = $this->getData(['user', $this->getUrl(2), 'password']);
			}
			// Modification du rang
			if($this->getUser('rank') === self::RANK_ADMIN) {
				$newRank = $this->getInput('userEditRank', helper::FILTER_INT);
			}
			else {
				$newRank = $this->getData(['user', $this->getUrl(2), 'rank']);
			}
			// Modifie l'utilisateur
			$this->setData([
				'user',
				$this->getUrl(2),
				[
					'name' => $this->getInput('userEditName'),
					'rank' => $newRank,
					'password' => $newPassword
				]
			]);
			return [
				'redirect' => $this->getUrl(),
				'notification' => 'Utilisateur modifié',
				'state' => true
			];
		}
		// Affichage du template
		else {
			return [
				'title' => $this->getData(['user', $this->getUrl(2), 'name']),
				'view' => true
			];
		}
	}

	/**
	 * Liste des utilisateurs
	 */
	public function index() {
		$userIdsNames = helper::arrayCollumn($this->getData(['user']), 'name');
		asort($userIdsNames);
		foreach($userIdsNames as $userId => $userName) {
			self::$users[] = [
				$userName,
				template::button('userEdit' . $userId, [
					'value' => template::ico('pencil'),
					'href' => helper::baseUrl() . 'user/edit/' . $userId
				]),
				template::button('userDelete' . $userId, [
					'value' => template::ico('cancel'),
					'href' => helper::baseUrl() . 'user/delete/' . $userId,
					'class' => 'userDelete'
				])
			];
		}
		return [
			'title' => 'Utilisateurs',
			'view' => true
		];
	}

	/**
	 * Connexion
	 */
	public function login() {
		// Soumission du formulaire
		if($this->isPost()) {
			// Connexion si les informations sont correctes
			if(
				$this->getData(['user', $this->getInput('userId')])
				AND $this->getData(['user', $this->getInput('userId'), 'password']) === hash('sha256', $this->getInput('userPassword'))
			) {
				$expire = $this->getInput('userLongTime') ? strtotime("+1 year") : 0;
				setcookie('ZWII_USER_ID', $this->getInput('userId'), $expire);
				setcookie('ZWII_USER_PASSWORD', hash('sha256', $this->getInput('userPassword')), $expire);
				return [
					'notification' => 'Connexion réussie',
					'redirect' => implode('/', array_slice(explode('/', $this->getUrl()), 2)),
					'state' => true
				];
			}
			// Sinon notification d'échec
			else {
				return [
					'notification' => 'Identifiant ou mot de passe incorrect',
					'title' => 'Connexion',
					'view' => true
				];
			}
		}
		// Affichage du template
		else {
			return [
				'title' => 'Connexion',
				'view' => true
			];
		}
	}

	/**
	 * Déconnexion
	 */
	public function logout() {
		helper::deleteCookie('ZWII_USER_ID');
		helper::deleteCookie('ZWII_USER_PASSWORD');
		return [
			'notification' => 'Déconnexion réussie',
			'redirect' => implode('/', array_slice(explode('/', $this->getUrl()), 2)),
			'state' => true
		];
	}

}