<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * The unapproved admin controller allows administrators to approve newly signed up members.
 * It is not accessible unless esoTalk.registration.requireConfirmation == approval.
 *
 * @package esoTalk
 */
class ETUnapprovedAdminController extends ETAdminController {


/**
 * Show a sheet containing a list of groups. Pretty simple, really!
 *
 * @return void
 */
public function index()
{
	$members = ET::memberModel()->get(array("confirmed" => false));

	$this->data("members", $members);
	$this->render("admin/unapproved");
}


/**
 * Approve a member.
 *
 * @param int $memberId The ID of the member to approve.
 * @return void
 */
public function approve($memberId)
{
	if (!$this->validateToken()) return;
	
	// Get this member's details. If it doesn't exist or is already approved, show an error.
	if (!($member = ET::memberModel()->getById((int)$memberId)) or $member["confirmed"]) {
		$this->redirect(URL("admin/unapproved"));
		return;
	}

	ET::memberModel()->updateById($memberId, array("confirmed" => true));

	// send an email here

	$this->message(T("message.changesSaved"), "success");
	$this->redirect(URL("admin/unapproved"));
}


/**
 * Deny a member; delete their account.
 *
 * @param int $memberId The ID of the member to deny.
 * @return void
 */
public function deny($memberId)
{
	// Get this member's details. If it doesn't exist or is already approved, show an error.
	if (!($member = ET::memberModel()->getById((int)$memberId)) or $member["confirmed"]) {
		$this->redirect(URL("admin/unapproved"));
		return;
	}

	ET::memberModel()->deleteById($memberId);

	$this->message(T("message.changesSaved"), "success");
	$this->redirect(URL("admin/unapproved"));
}

}
