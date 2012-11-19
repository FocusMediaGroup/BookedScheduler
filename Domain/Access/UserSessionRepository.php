<?php
/**
Copyright 2012 Nick Korbel

This file is part of phpScheduleIt.

phpScheduleIt is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

phpScheduleIt is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once(ROOT_DIR . 'Domain/Values/WebService/WebServiceUserSession.php');

interface IUserSessionRepository
{
	/**
	 * @param int $userId
	 * @return WebServiceUserSession|null
	 */
	public function LoadByUserId($userId);

	/**
	 * @param string $sessionToken
	 * @return WebServiceUserSession
	 */
	public function LoadBySessionToken($sessionToken);

	/**
	 * @param WebServiceUserSession $session
	 * @return void
	 */
	public function Add(WebServiceUserSession $session);

	/**
	 * @param WebServiceUserSession $session
	 * @return void
	 */
	public function Update(WebServiceUserSession $session);

	/**
	 * @param WebServiceUserSession $session
	 * @return void
	 */
	public function Delete(WebServiceUserSession $session);
}

class UserSessionRepository implements IUserSessionRepository
{
	public function LoadByUserId($userId)
	{
		$reader = ServiceLocator::GetDatabase()->Query(new GetUserSessionByUserIdCommand($userId));
		if ($row = $reader->GetRow())
		{
			return unserialize($row[ColumnNames::USER_SESSION]);
		}
		return null;
	}

	public function LoadBySessionToken($sessionToken)
	{
		$reader = ServiceLocator::GetDatabase()->Query(new GetUserSessionBySessionTokenCommand($sessionToken));
		if ($row = $reader->GetRow())
		{
			return unserialize($row[ColumnNames::USER_SESSION]);
		}
		return null;
	}

	public function Add(WebServiceUserSession $session)
	{
		$serializedSession = serialize($session);
		ServiceLocator::GetDatabase()->Execute(new AddUserSessionCommand($session->UserId, $session->SessionToken, Date::Now(), $serializedSession));
	}

	public function Update(WebServiceUserSession $session)
	{
		$serializedSession = serialize($session);
		ServiceLocator::GetDatabase()->Execute(new UpdateUserSessionCommand($session->UserId, $session->SessionToken, Date::Now(), $serializedSession));
	}

	public function Delete(WebServiceUserSession $session)
	{
		ServiceLocator::GetDatabase()->Execute(new DeleteUserSessionCommand($session->SessionToken));
	}
}

?>