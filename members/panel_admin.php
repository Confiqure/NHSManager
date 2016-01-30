					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-lock fa-fw"></i> Admin Panel
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs">
								<li<?php if ($account['role'] !== 'Administrator' && $account['role'] !== 'Parliamentarian' && $account['role'] !== 'Secretary') echo ' class="active"';?>><a href="#event" data-toggle="tab">Add an event</a></li>
								<li><a href="#tutor" data-toggle="tab">Request a tutor</a></li>
								<?php
								if ($account['role'] === "Administrator" || $account['role'] === "Parliamentarian") echo '<li' . ($account['role'] === "Parliamentarian" ? ' class="active"' : '') . '><a href="#approve" data-toggle="tab">Approve hours</a></li>';
								if ($account['role'] === "Administrator" || $account['role'] === "Secretary") echo '<li' . ($account['role'] === "Secretary" ? ' class="active"' : '') . '><a href="#minutes" data-toggle="tab">Upload meeting minutes</a></li>';
								if ($account['role'] === "Administrator") echo '<li class="active"><a href="#settings" data-toggle="tab">Admin</a></li>';
								?>
							</ul>
								<!-- Tab panes -->
							<div class="tab-content">
								<div class="tab-pane fade<?php if ($account['role'] !== 'Administrator' && $account['role'] !== 'Parliamentarian' && $account['role'] !== 'Secretary') echo ' in active';?>" id="event">
									<h3>Add an event</h3>
									<form role="form" action="addEvent.php" method="POST">
										<div class="row">
											<div class="col-sm-3">
												<div class="form-group">
													<label>Event Title</label>
													<input class="form-control" name="title" maxlength="32" placeholder="Title the event" required />
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label>Event Date</label>
													<input class="form-control" type="date" name="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" required />
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label>Badge Color</label>
													<select class="form-control" name="color">
														<option selected>Red</option>
														<option>Green</option>
														<option>Blue</option>
														<option>Yellow</option>
													</select>
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label>Badge Icon Code <sup><a href="../support/icons.html" target="_blank">[?]</a></sup></label>
													<input class="form-control" name="icon" maxlength="32" placeholder="Click the '?' for help" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<label>Description</label>
											<textarea class="form-control" name="description" rows="3" placeholder="Add a brief description so that people know what the event is!" required></textarea>
										</div>
										<button type="submit" class="btn btn-default">Add to Timeline</button>
									</form>
								</div>
								<div class="tab-pane fade" id="tutor">
									<h3>Request a tutor</h3>
									<form role="form" action="addTutee.php" method="POST">
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input class="form-control" name="name" maxlength="32" placeholder="Student Name" required />
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<input class="form-control" name="grade" maxlength="2" placeholder="Grade" required />
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<input class="form-control" name="contact" maxlength="256" placeholder="Contact info" required />
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<input class="form-control" name="subjects" maxlength="256" placeholder="Subjects" required />
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<input class="form-control" name="free" maxlength="256" placeholder="Availability" required />
												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-default">Post to Website</button>
									</form>
								</div>
								<?php
									if ($account['role'] === "Administrator" || $account['role'] === "Parliamentarian") { echo '
								<div class="tab-pane fade' . ($account['role'] === 'Parliamentarian' ? ' in active' : '') . '" id="approve">
									<h3>Approve hours</h3>
									<div class="table-responsive">
										<table class="table table-bordered table-hover table-striped">
											<thead>
												<tr>
													<th style="vertical-align:middle"><center>Name</center></th>
													<th style="vertical-align:middle"><center>Date</center></th>
													<th style="vertical-align:middle"><center>Service</center></th>
													<th style="vertical-align:middle"><center>Hours</center></th>
													<th style="vertical-align:middle"><center>Description</center></th>
													<th style="vertical-align:middle"><center>Contact Info.</center></th>
													<th style="vertical-align:middle"><center>Approve?</center></th>
												</tr>
											</thead>
											<tbody>';
														if (sizeof($pending) === 0) {
												echo '<tr><td colspan="7">No one needs service hours approved.</td></tr>';
														} else {
															for ($i = 0; $i < sizeof($pending); $i++) {
																$entries = explode(';', $pending[$i][2]);
																$count = 0;
																foreach ($entries as $entry) {
																	if (strlen($entry) === 0) continue;
																	$components = explode(',', $entry);
																	$desc = $components[3];
																	$desc = str_replace('/[c]/', ',', $desc);
																	$desc = str_replace('/[s]/', ';', $desc);
																	$desc = str_replace('/[q]/', '"', $desc);
																	$contact = $components[4];
																	$contact = str_replace('/[c]/', ',', $contact);
																	$contact = str_replace('/[s]/', ';', $contact);
																	$contact = str_replace('/[q]/', '"', $contact);
																	echo '
												<tr>
													<td style="vertical-align:middle"><center>' . $pending[$i][1] . '</center></td>
													<td style="vertical-align:middle"><center>' . $components[0] . '</center></td>
													<td style="vertical-align:middle"><center>' . ($components[1] === 'tutoring' ? 'Tutoring' : 'Community Service') . '</center></td>
													<td style="vertical-align:middle"><center>' . $components[2] . '</center></td>
													<td>' . $desc . '</td>
													<td style="vertical-align:middle"><center>' . $contact . '</center></td>
													<td style="vertical-align:middle"><center><a href="process.php?username=' . $pending[$i][0] . '&count=' . $count . '&state=true"><i class="fa fa-check fa-fw"></i></a>&nbsp;&nbsp;&nbsp;<a href="process.php?username=' . $pending[$i][0] . '&count=' . $count . '&state=false"><i class="fa fa-times fa-fw"></i></a></center></td>
												</tr>' . "\n";
																	$count++;
																}
															}
														}
											echo '</tbody>
										</table>
									</div>
								</div>';
										}
									if ($account['role'] === "Administrator" || $account['role'] === "Secretary") { echo '
								<div class="tab-pane fade' . ($account['role'] === 'Secretary' ? ' in active' : '') . '" id="minutes">
									<h3>Upload meeting minutes</h3>
									<form role="form" action="addMinutes.php" method="POST">
										<div class="row">
											<div class="col-sm-3">
												<div class="form-group">
													<label>Meeting Date</label>
													<input class="form-control" name="date" maxlength="32" placeholder="Month Date, Year" value="' . date('F d, Y') . '" required />
												</div>
											</div>
											<div class="col-sm-9">
												<div class="form-group">
													<label>URL to Minutes</label>
													<input class="form-control" name="link" maxlength="256" placeholder="Upload the minutes to Google Drive, give it public access via URL, and link it here" required />
												</div>
											</div>
										</div>
										<div class="form-group">
											<label>Absentees</label>
											<textarea class="form-control" name="absent" rows="3" maxlength="1024" placeholder="List all members who were absent during the meeting separated by commas."></textarea>
										</div>
										<button type="submit" class="btn btn-default">Upload</button>
									</form>
								</div>';
									}
									if ($account['role'] === "Administrator") { echo '
								<div class="tab-pane fade in active" id="settings">
									<h3>Admin</h3>
									<form role="form" action="changeVar.php?key=announcement" method="POST">
										<label>Update the announcement</label>
										<div class="row">
											<div class="col-sm-10">
												<div class="form-group">
													<input class="form-control" name="value" maxlength="1024" placeholder="Change the current announcement by typing the new one here" value="" required />
												</div>
											</div>
											<div class="col-sm-2">
												<button type="submit" class="btn btn-default">Update</button>
											</div>
										</div>
									</form>
									<form role="form" action="changeVar.php?key=mention" method="POST">
										<div class="form-group">
											<label>Honorable Mention</label>
											<textarea class="form-control" name="value" rows="5" maxlength="1024" placeholder="Talk about who it is that deserves an Honorable Mention on the home page! What you type here is exactly what appears on the home screen." required></textarea>
										</div>
										<button type="submit" class="btn btn-default">Change</button>
									</form>
								</div>';
									}
								?>
							</div>
						</div>
						<!-- /.panel-body -->
					</div>
					<!-- /.panel -->