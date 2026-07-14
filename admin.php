<?php
session_start();

// Admin credentials
$admin_user = 'hunain';
$admin_pass = 'hunain123';

// Path to data file
$data_file = __DIR__ . '/data/portfolio_data.json';

// Load existing data
if (file_exists($data_file)) {
    $data = json_decode(file_get_contents($data_file), true);
} else {
    $data = [
        'profile' => [
            'name' => '', 'subtitle' => '', 'bio_summary' => '', 'bio_details' => '', 'vision' => '',
            'profile_pic' => 'images/portrait.jpg', 'resume_url' => 'cv/resume.pdf', 'contact_email' => '', 'phone' => '',
            'titles' => []
        ],
        'achievements' => [],
        'timeline' => [],
        'gallery' => [],
        'videos' => []
    ];
}

// Handle login
$error = '';
$success = '';

if (isset($_POST['login'])) {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($user === $admin_user && $pass === $admin_pass) {
        $_SESSION['loggedin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid username or password!';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Check authentication
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Helper function to save JSON data
function save_data($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// Handle POST actions if authenticated
if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // 1. Update Profile Information
    if ($action === 'update_profile') {
        $data['profile']['name'] = trim($_POST['name']);
        $data['profile']['subtitle'] = trim($_POST['subtitle']);
        $data['profile']['bio_summary'] = trim($_POST['bio_summary']);
        $data['profile']['bio_details'] = trim($_POST['bio_details']);
        $data['profile']['vision'] = trim($_POST['vision']);
        $data['profile']['contact_email'] = trim($_POST['contact_email']);
        $data['profile']['phone'] = trim($_POST['phone']);

        // Handle skills stats
        if (isset($_POST['skills_stats']) && is_array($_POST['skills_stats'])) {
            $data['skills_stats'] = [
                'Agility' => (int)$_POST['skills_stats']['Agility'],
                'Speed' => (int)$_POST['skills_stats']['Speed'],
                'Power' => (int)$_POST['skills_stats']['Power'],
                'Endurance' => (int)$_POST['skills_stats']['Endurance'],
                'Tactics' => (int)$_POST['skills_stats']['Tactics'],
                'Technique' => (int)$_POST['skills_stats']['Technique']
            ];
        }

        // Handle titles tags array
        $titles_raw = isset($_POST['titles']) ? trim($_POST['titles']) : '';
        $data['profile']['titles'] = array_filter(array_map('trim', explode(',', $titles_raw)));

        // Handle Profile picture upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $target = 'images/portrait.' . $ext;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], __DIR__ . '/' . $target)) {
                $data['profile']['profile_pic'] = $target;
            }
        }

        // Handle CV/Resume upload
        if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] === UPLOAD_ERR_OK) {
            if (!file_exists(__DIR__ . '/cv')) {
                mkdir(__DIR__ . '/cv', 0777, true);
            }
            $target = 'cv/resume.pdf';
            if (move_uploaded_file($_FILES['resume_file']['tmp_name'], __DIR__ . '/' . $target)) {
                $data['profile']['resume_url'] = $target;
            }
        }

        if (save_data($data_file, $data)) {
            $success = 'Profile details updated successfully!';
        } else {
            $error = 'Failed to save profile changes!';
        }
    }

    // 2. Add Achievement
    if ($action === 'add_achievement') {
        $new_id = 1;
        if (!empty($data['achievements'])) {
            $new_id = max(array_column($data['achievements'], 'id')) + 1;
        }

        $data['achievements'][] = [
            'id' => $new_id,
            'icon' => trim($_POST['icon']),
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description'])
        ];

        if (save_data($data_file, $data)) {
            $success = 'Achievement added successfully!';
        } else {
            $error = 'Failed to add achievement!';
        }
    }

    // 3. Edit Achievement
    if ($action === 'edit_achievement') {
        $id = (int)$_POST['id'];
        foreach ($data['achievements'] as &$ach) {
            if ($ach['id'] === $id) {
                $ach['icon'] = trim($_POST['icon']);
                $ach['title'] = trim($_POST['title']);
                $ach['description'] = trim($_POST['description']);
                break;
            }
        }

        if (save_data($data_file, $data)) {
            $success = 'Achievement updated successfully!';
        } else {
            $error = 'Failed to update achievement!';
        }
    }

    // 4. Delete Achievement
    if ($action === 'delete_achievement') {
        $id = (int)$_POST['id'];
        $data['achievements'] = array_values(array_filter($data['achievements'], function($ach) use ($id) {
            return $ach['id'] !== $id;
        }));

        if (save_data($data_file, $data)) {
            $success = 'Achievement deleted successfully!';
        } else {
            $error = 'Failed to delete achievement!';
        }
    }

    // 5. Add Timeline Event
    if ($action === 'add_timeline') {
        $new_id = 1;
        if (!empty($data['timeline'])) {
            $new_id = max(array_column($data['timeline'], 'id')) + 1;
        }

        $data['timeline'][] = [
            'id' => $new_id,
            'year' => trim($_POST['year']),
            'title' => trim($_POST['title']),
            'subtitle' => '',
            'description' => trim($_POST['description'])
        ];

        if (save_data($data_file, $data)) {
            $success = 'Timeline event added successfully!';
        } else {
            $error = 'Failed to add timeline event!';
        }
    }

    // 6. Edit Timeline Event
    if ($action === 'edit_timeline') {
        $id = (int)$_POST['id'];
        foreach ($data['timeline'] as &$item) {
            if ($item['id'] === $id) {
                $item['year'] = trim($_POST['year']);
                $item['title'] = trim($_POST['title']);
                $item['description'] = trim($_POST['description']);
                break;
            }
        }

        if (save_data($data_file, $data)) {
            $success = 'Timeline event updated successfully!';
        } else {
            $error = 'Failed to update timeline event!';
        }
    }

    // 7. Delete Timeline Event
    if ($action === 'delete_timeline') {
        $id = (int)$_POST['id'];
        $data['timeline'] = array_values(array_filter($data['timeline'], function($item) use ($id) {
            return $item['id'] !== $id;
        }));

        if (save_data($data_file, $data)) {
            $success = 'Timeline event deleted successfully!';
        } else {
            $error = 'Failed to delete timeline event!';
        }
    }

    // 8. Upload Gallery Photo
    if ($action === 'add_gallery') {
        if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
            $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($_FILES['photo_file']['name']));
            // Avoid conflict
            $filename = time() . '_' . $filename;
            $target = 'images/' . $filename;

            if (move_uploaded_file($_FILES['photo_file']['tmp_name'], __DIR__ . '/' . $target)) {
                $new_id = 1;
                if (!empty($data['gallery'])) {
                    $new_id = max(array_column($data['gallery'], 'id')) + 1;
                }

                $data['gallery'][] = [
                    'id' => $new_id,
                    'category' => trim($_POST['category']),
                    'type' => 'image',
                    'src' => $target,
                    'alt' => trim($_POST['title']),
                    'title' => trim($_POST['title'])
                ];

                if (save_data($data_file, $data)) {
                    $success = 'Gallery image uploaded successfully!';
                } else {
                    $error = 'Failed to save gallery entry!';
                }
            } else {
                $error = 'Failed to move uploaded photo file!';
            }
        } else {
            $error = 'Please select a valid image file to upload!';
        }
    }

    // 9. Delete Gallery Photo
    if ($action === 'delete_gallery') {
        $id = (int)$_POST['id'];
        $target_src = '';

        foreach ($data['gallery'] as $item) {
            if ($item['id'] === $id) {
                $target_src = $item['src'];
                break;
            }
        }

        $data['gallery'] = array_values(array_filter($data['gallery'], function($item) use ($id) {
            return $item['id'] !== $id;
        }));

        if (save_data($data_file, $data)) {
            // Delete file from disk
            if (!empty($target_src) && file_exists(__DIR__ . '/' . $target_src)) {
                @unlink(__DIR__ . '/' . $target_src);
            }
            $success = 'Gallery image deleted successfully!';
        } else {
            $error = 'Failed to delete gallery image entry!';
        }
    }

    // 10. Upload Video
    if ($action === 'add_video') {
        $src = '';
        if (isset($_POST['video_source_type']) && $_POST['video_source_type'] === 'upload') {
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                if (!file_exists(__DIR__ . '/videos')) {
                    mkdir(__DIR__ . '/videos', 0777, true);
                }
                $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($_FILES['video_file']['name']));
                $filename = time() . '_' . $filename;
                $target = 'videos/' . $filename;

                if (move_uploaded_file($_FILES['video_file']['tmp_name'], __DIR__ . '/' . $target)) {
                    $src = $target;
                } else {
                    $error = 'Failed to move uploaded video file!';
                }
            } else {
                $error = 'Please select a valid video file to upload!';
            }
        } else {
            $src = trim($_POST['video_url']);
        }

        if (!empty($src) && empty($error)) {
            $new_id = 1;
            if (!empty($data['videos'])) {
                $new_id = max(array_column($data['videos'], 'id')) + 1;
            }

            $data['videos'][] = [
                'id' => $new_id,
                'category' => trim($_POST['category']),
                'src' => $src,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description'])
            ];

            if (save_data($data_file, $data)) {
                $success = 'Video added successfully!';
            } else {
                $error = 'Failed to save video entry!';
            }
        }
    }

    // 11. Delete Video
    if ($action === 'delete_video') {
        $id = (int)$_POST['id'];
        $target_src = '';

        foreach ($data['videos'] as $item) {
            if ($item['id'] === $id) {
                $target_src = $item['src'];
                break;
            }
        }

        $data['videos'] = array_values(array_filter($data['videos'], function($item) use ($id) {
            return $item['id'] !== $id;
        }));

        if (save_data($data_file, $data)) {
            // Delete local file if it resides in videos/
            if (!empty($target_src) && strpos($target_src, 'videos/') === 0 && file_exists(__DIR__ . '/' . $target_src)) {
                @unlink(__DIR__ . '/' . $target_src);
            }
            $success = 'Video entry deleted successfully!';
        } else {
            $error = 'Failed to delete video entry!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M. Husnain - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php if (!$is_logged_in): ?>
    <!-- Login Screen -->
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">ATHLETE<span>.</span>ADMIN</div>
                <div class="login-subtitle">Sign in to manage portfolio content</div>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="admin.php">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                </div>
                <button type="submit" class="btn-admin-submit">Access Dashboard</button>
            </form>
        </div>
    </div>

<?php else: ?>
    <!-- Dashboard Screen -->
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a class="sidebar-logo">HUSNAIN<span>.</span>CMS</a>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a class="sidebar-link active" data-tab-link="dashboard"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-link" data-tab-link="profile"><i class="fas fa-user-circle"></i><span>Profile Info</span></a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-link" data-tab-link="achievements"><i class="fas fa-trophy"></i><span>Achievements</span></a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-link" data-tab-link="timeline"><i class="fas fa-route"></i><span>Timeline</span></a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-link" data-tab-link="gallery"><i class="fas fa-images"></i><span>Photo Gallery</span></a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-link" data-tab-link="videos"><i class="fas fa-video"></i><span>Video Highlights</span></a>
                </li>
                <li class="sidebar-menu-item" style="margin-top: 40px;">
                    <a href="index.php" target="_blank" class="sidebar-link"><i class="fas fa-globe"></i><span>Visit Site</span></a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin.php?logout=1" class="sidebar-link logout-link"><i class="fas fa-sign-out-alt"></i><span>Sign Out</span></a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="admin-title">
                    <h2>Portfolio Dashboard</h2>
                    <p>Manage and customize dynamic page content</p>
                </div>
            </header>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($error) && !isset($_POST['login'])): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- TAB 0: Dashboard Overview -->
            <section class="admin-panel active" id="panel-dashboard">
                <!-- Stats Grid -->
                <div class="admin-stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary"><i class="fas fa-trophy"></i></div>
                        <div>
                            <div class="stat-number"><?php echo count($data['achievements']); ?></div>
                            <div class="stat-label">Achievements</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon secondary"><i class="fas fa-images"></i></div>
                        <div>
                            <div class="stat-number"><?php echo count($data['gallery']); ?></div>
                            <div class="stat-label">Photos</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="color: #3b82f6; background-color: rgba(59, 130, 246, 0.1);"><i class="fas fa-video"></i></div>
                        <div>
                            <div class="stat-number"><?php echo count($data['videos']); ?></div>
                            <div class="stat-label">Videos</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="color: #a855f7; background-color: rgba(168, 85, 247, 0.1);"><i class="fas fa-route"></i></div>
                        <div>
                            <div class="stat-number"><?php echo count($data['timeline']); ?></div>
                            <div class="stat-label">Milestones</div>
                        </div>
                    </div>
                </div>

                <div class="form-row" style="grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <!-- Skills Rating Radar Chart -->
                    <div class="card-admin" style="height: 480px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="card-admin-title">Athletic Skills Chart</div>
                            <div style="position: relative; height: 280px; width: 100%;">
                                <canvas id="adminRadarChart"></canvas>
                            </div>
                        </div>
                        <a class="btn-admin btn-admin-secondary" style="width: 100%;" onclick="document.querySelector('[data-tab-link=profile]').click();">
                            <i class="fas fa-edit"></i> Edit Ratings in Profile
                        </a>
                    </div>

                    <!-- Content Category Doughnut Chart -->
                    <div class="card-admin" style="height: 480px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="card-admin-title">Content Composition</div>
                            <div style="position: relative; height: 280px; width: 100%;">
                                <canvas id="adminDoughnutChart"></canvas>
                            </div>
                        </div>
                        <div style="text-align: center; color: var(--clr-admin-text-muted); font-size: 13px; padding-bottom: 10px;">
                            Distribution of items across Tennis, Cricket, and Certificates.
                        </div>
                    </div>
                </div>
            </section>

            <!-- TAB 1: Profile Info -->
            <section class="admin-panel" id="panel-profile">
                <div class="card-admin">
                    <div class="card-admin-title">Edit Biography & Details</div>
                    <form method="POST" action="admin.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="profile_name">Full Name</label>
                                <input type="text" id="profile_name" name="name" class="form-control form-control-no-icon" value="<?php echo htmlspecialchars($data['profile']['name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="profile_subtitle">Subtitle / Sports Tag</label>
                                <input type="text" id="profile_subtitle" name="subtitle" class="form-control form-control-no-icon" value="<?php echo htmlspecialchars($data['profile']['subtitle']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="profile_titles">Role Titles (Comma separated tags for typing animation)</label>
                            <input type="text" id="profile_titles" name="titles" class="form-control form-control-no-icon" value="<?php echo htmlspecialchars(implode(', ', $data['profile']['titles'])); ?>" placeholder="e.g. Soft Tennis Player, Cricketer, CS Student" required>
                        </div>

                        <div class="form-group">
                            <label for="profile_bio_summary">Bio Summary (Short intro description)</label>
                            <textarea id="profile_bio_summary" name="bio_summary" class="form-control form-control-no-icon" required><?php echo htmlspecialchars($data['profile']['bio_summary']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="profile_bio_details">Journey Details (Full text for About section)</label>
                            <textarea id="profile_bio_details" name="bio_details" class="form-control form-control-no-icon" required><?php echo htmlspecialchars($data['profile']['bio_details']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="profile_vision">Career Vision (Vision block quotes)</label>
                            <textarea id="profile_vision" name="vision" class="form-control form-control-no-icon" required><?php echo htmlspecialchars($data['profile']['vision']); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="profile_email">Contact Email</label>
                                <input type="email" id="profile_email" name="contact_email" class="form-control form-control-no-icon" value="<?php echo htmlspecialchars($data['profile']['contact_email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="profile_phone">Contact Phone</label>
                                <input type="text" id="profile_phone" name="phone" class="form-control form-control-no-icon" value="<?php echo htmlspecialchars($data['profile']['phone']); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Profile Picture (Square JPG/PNG)</label>
                                <div class="file-upload-wrapper">
                                    <i class="fas fa-image"></i>
                                    <p>Click or drag image here to upload new profile pic</p>
                                    <input type="file" name="profile_pic" accept="image/*">
                                </div>
                                <p class="upload-instructions">Current: <?php echo htmlspecialchars($data['profile']['profile_pic']); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Resume / CV (PDF Format)</label>
                                <div class="file-upload-wrapper">
                                    <i class="fas fa-file-pdf"></i>
                                    <p>Click or drag PDF here to upload new Resume</p>
                                    <input type="file" name="resume_file" accept=".pdf">
                                </div>
                                <p class="upload-instructions">Current: <?php echo htmlspecialchars($data['profile']['resume_url']); ?></p>
                            </div>
                        </div>

                        <div class="card-admin" style="margin-top: 30px; padding: 20px; background-color: rgba(255,255,255,0.01); border-color: rgba(255,255,255,0.05); border-radius: var(--radius-admin);">
                            <h4 style="font-size: 15px; margin-bottom: 20px; text-transform: uppercase; color: var(--clr-admin-secondary); letter-spacing: 1px;">Athletic Performance Ratings (%)</h4>
                            <div class="form-row" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px;">
                                <div class="form-group">
                                    <label for="skill_agility">Agility</label>
                                    <input type="number" id="skill_agility" name="skills_stats[Agility]" class="form-control form-control-no-icon" value="<?php echo (int)($data['skills_stats']['Agility'] ?? 90); ?>" min="0" max="100" required>
                                </div>
                                <div class="form-group">
                                    <label for="skill_speed">Speed</label>
                                    <input type="number" id="skill_speed" name="skills_stats[Speed]" class="form-control form-control-no-icon" value="<?php echo (int)($data['skills_stats']['Speed'] ?? 85); ?>" min="0" max="100" required>
                                </div>
                                <div class="form-group">
                                    <label for="skill_power">Power</label>
                                    <input type="number" id="skill_power" name="skills_stats[Power]" class="form-control form-control-no-icon" value="<?php echo (int)($data['skills_stats']['Power'] ?? 80); ?>" min="0" max="100" required>
                                </div>
                                <div class="form-group">
                                    <label for="skill_endurance">Endurance</label>
                                    <input type="number" id="skill_endurance" name="skills_stats[Endurance]" class="form-control form-control-no-icon" value="<?php echo (int)($data['skills_stats']['Endurance'] ?? 88); ?>" min="0" max="100" required>
                                </div>
                                <div class="form-group">
                                    <label for="skill_tactics">Tactics</label>
                                    <input type="number" id="skill_tactics" name="skills_stats[Tactics]" class="form-control form-control-no-icon" value="<?php echo (int)($data['skills_stats']['Tactics'] ?? 92); ?>" min="0" max="100" required>
                                </div>
                                <div class="form-group">
                                    <label for="skill_technique">Technique</label>
                                    <input type="number" id="skill_technique" name="skills_stats[Technique]" class="form-control form-control-no-icon" value="<?php echo (int)($data['skills_stats']['Technique'] ?? 87); ?>" min="0" max="100" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-admin btn-admin-primary"><i class="fas fa-save"></i> Save Profile Details</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- TAB 2: Achievements -->
            <section class="admin-panel" id="panel-achievements">
                <div class="card-admin">
                    <div class="card-admin-title">Add Key Achievement</div>
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="action" value="add_achievement">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ach_title">Title</label>
                                <input type="text" id="ach_title" name="title" class="form-control form-control-no-icon" required>
                            </div>
                            <div class="form-group">
                                <label for="ach_icon">Icon Class (FontAwesome)</label>
                                <input type="text" id="ach_icon" name="icon" class="form-control form-control-no-icon" placeholder="fa-solid fa-trophy" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ach_desc">Description</label>
                            <textarea id="ach_desc" name="description" class="form-control form-control-no-icon" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-admin btn-admin-primary"><i class="fas fa-plus"></i> Add Accolade</button>
                        </div>
                    </form>
                </div>

                <div class="card-admin">
                    <div class="card-admin-title">Manage Achievements</div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Badge</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['achievements'] as $ach): ?>
                                    <tr>
                                        <td><i class="<?php echo htmlspecialchars($ach['icon']); ?>" style="font-size: 20px;"></i></td>
                                        <td><strong><?php echo htmlspecialchars($ach['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(substr($ach['description'], 0, 80)) . '...'; ?></td>
                                        <td style="text-align: right;">
                                            <form method="POST" action="admin.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_achievement">
                                                <input type="hidden" name="id" value="<?php echo $ach['id']; ?>">
                                                <button type="submit" class="btn-admin btn-admin-danger" onclick="return confirm('Are you sure you want to delete this achievement?');"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 3: Timeline -->
            <section class="admin-panel" id="panel-timeline">
                <div class="card-admin">
                    <div class="card-admin-title">Add Timeline Career Entry</div>
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="action" value="add_timeline">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="time_year">Year</label>
                                <input type="text" id="time_year" name="year" class="form-control form-control-no-icon" placeholder="e.g. 2026 or Future Goals" required>
                            </div>
                            <div class="form-group">
                                <label for="time_title">Event Title</label>
                                <input type="text" id="time_title" name="title" class="form-control form-control-no-icon" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="time_desc">Description Details</label>
                            <textarea id="time_desc" name="description" class="form-control form-control-no-icon" required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-admin btn-admin-primary"><i class="fas fa-plus"></i> Add Event Node</button>
                        </div>
                    </form>
                </div>

                <div class="card-admin">
                    <div class="card-admin-title">Manage Chronology Timeline</div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Title</th>
                                    <th>Description Details</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['timeline'] as $item): ?>
                                    <tr>
                                        <td><span class="badge-category cert"><?php echo htmlspecialchars($item['year']); ?></span></td>
                                        <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(substr($item['description'], 0, 80)) . '...'; ?></td>
                                        <td style="text-align: right;">
                                            <form method="POST" action="admin.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_timeline">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn-admin btn-admin-danger" onclick="return confirm('Are you sure you want to delete this event?');"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 4: Photo Gallery -->
            <section class="admin-panel" id="panel-gallery">
                <div class="card-admin">
                    <div class="card-admin-title">Upload New Gallery Photo</div>
                    <form method="POST" action="admin.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_gallery">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gal_title">Photo Title</label>
                                <input type="text" id="gal_title" name="title" class="form-control form-control-no-icon" required>
                            </div>
                            <div class="form-group">
                                <label for="gal_cat">Category Tab</label>
                                <select id="gal_cat" name="category" class="form-control form-control-no-icon" style="background-color:#1c1c1f;">
                                    <option value="tennis">Soft Tennis</option>
                                    <option value="cricket">Cricket</option>
                                    <option value="cert">Certificates</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Photo File (Square or Landscape, maximum 10MB)</label>
                            <div class="file-upload-wrapper">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to select image file to upload</p>
                                <input type="file" name="photo_file" accept="image/*" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-admin btn-admin-primary"><i class="fas fa-upload"></i> Upload Image</button>
                        </div>
                    </form>
                </div>

                <div class="card-admin">
                    <div class="card-admin-title">Manage Gallery Images (Total: <?php echo count($data['gallery']); ?>)</div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Image Path</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['gallery'] as $item): ?>
                                    <tr>
                                        <td><img src="<?php echo htmlspecialchars($item['src']); ?>" class="thumbnail-preview" alt=""></td>
                                        <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                                        <td><span class="badge-category <?php echo htmlspecialchars($item['category']); ?>"><?php echo htmlspecialchars($item['category']); ?></span></td>
                                        <td><code><?php echo htmlspecialchars($item['src']); ?></code></td>
                                        <td style="text-align: right;">
                                            <form method="POST" action="admin.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_gallery">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn-admin btn-admin-danger" onclick="return confirm('Are you sure you want to delete this gallery item? This will delete the image file from disk.');"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 5: Video Highlights -->
            <section class="admin-panel" id="panel-videos">
                <div class="card-admin">
                    <div class="card-admin-title">Add Video Highlight</div>
                    <form method="POST" action="admin.php" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_video">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="vid_title">Video Title</label>
                                <input type="text" id="vid_title" name="title" class="form-control form-control-no-icon" required>
                            </div>
                            <div class="form-group">
                                <label for="vid_cat">Category Tag</label>
                                <select id="vid_cat" name="category" class="form-control form-control-no-icon" style="background-color:#1c1c1f;">
                                    <option value="tennis">Soft Tennis</option>
                                    <option value="cricket">Cricket</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="vid_desc">Video Description Details</label>
                            <input type="text" id="vid_desc" name="description" class="form-control form-control-no-icon" required>
                        </div>
                        <div class="form-group">
                            <label for="video_source_type">Video Source Type</label>
                            <select id="video_source_type" name="video_source_type" class="form-control form-control-no-icon" style="background-color:#1c1c1f;" onchange="toggleVideoSourceInput(this.value)">
                                <option value="upload">Upload Video File (.mp4)</option>
                                <option value="url">External Video Link / YouTube Embed URL</option>
                            </select>
                        </div>
                        <div class="form-group" id="video-upload-group">
                            <label>Video File (MP4, maximum 100MB)</label>
                            <div class="file-upload-wrapper">
                                <i class="fas fa-file-video"></i>
                                <p>Click to select video file (.mp4)</p>
                                <input type="file" name="video_file" accept="video/mp4">
                            </div>
                        </div>
                        <div class="form-group" id="video-url-group" style="display:none;">
                            <label for="video_url">Video Stream/Embed Link</label>
                            <input type="url" id="video_url" name="video_url" class="form-control form-control-no-icon" placeholder="e.g. videos/custom_match.mp4 or https://youtube.com/embed/...">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-admin btn-admin-primary"><i class="fas fa-plus"></i> Link Video Highlight</button>
                        </div>
                    </form>
                </div>

                <div class="card-admin">
                    <div class="card-admin-title">Manage Videos (Total: <?php echo count($data['videos']); ?>)</div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Format</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Source Link / Path</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['videos'] as $item): ?>
                                    <tr>
                                        <td><i class="fas fa-play-circle video-preview-icon"></i></td>
                                        <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                                        <td><span class="badge-category <?php echo htmlspecialchars($item['category']); ?>"><?php echo htmlspecialchars($item['category']); ?></span></td>
                                        <td><code><?php echo htmlspecialchars($item['src']); ?></code></td>
                                        <td style="text-align: right;">
                                            <form method="POST" action="admin.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_video">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn-admin btn-admin-danger" onclick="return confirm('Are you sure you want to delete this video highlight? This will remove local files from videos/ folder.');"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Script to toggle tab displays and video uploads -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Tab switching logic
        const tabLinks = document.querySelectorAll("[data-tab-link]");
        const panels = document.querySelectorAll(".admin-panel");

        tabLinks.forEach(link => {
            link.addEventListener("click", () => {
                const targetPanel = link.getAttribute("data-tab-link");

                // Remove active classes
                tabLinks.forEach(tl => tl.classList.remove("active"));
                panels.forEach(p => p.classList.remove("active"));

                // Add active classes
                link.classList.add("active");
                document.getElementById("panel-" + targetPanel).classList.add("active");
            });
        });

        // Initialize Admin Dashboard Charts
        <?php
        $tennis_count = 0;
        $cricket_count = 0;
        $cert_count = 0;
        foreach ($data['gallery'] as $item) {
            if ($item['category'] === 'tennis') $tennis_count++;
            elseif ($item['category'] === 'cricket') $cricket_count++;
            elseif ($item['category'] === 'cert') $cert_count++;
        }
        foreach ($data['videos'] as $item) {
            if ($item['category'] === 'tennis') $tennis_count++;
            elseif ($item['category'] === 'cricket') $cricket_count++;
        }
        ?>

        const radarCtx = document.getElementById('adminRadarChart');
        if (radarCtx) {
            new Chart(radarCtx, {
                type: 'radar',
                data: {
                    labels: ['Agility', 'Speed', 'Power', 'Endurance', 'Tactics', 'Technique'],
                    datasets: [{
                        label: 'Skills %',
                        data: [
                            <?php echo (int)($data['skills_stats']['Agility'] ?? 90); ?>,
                            <?php echo (int)($data['skills_stats']['Speed'] ?? 85); ?>,
                            <?php echo (int)($data['skills_stats']['Power'] ?? 80); ?>,
                            <?php echo (int)($data['skills_stats']['Endurance'] ?? 88); ?>,
                            <?php echo (int)($data['skills_stats']['Tactics'] ?? 92); ?>,
                            <?php echo (int)($data['skills_stats']['Technique'] ?? 87); ?>
                        ],
                        backgroundColor: 'rgba(0, 200, 83, 0.15)',
                        borderColor: '#00c853',
                        borderWidth: 2,
                        pointBackgroundColor: '#ffd700',
                        pointBorderColor: '#121214'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { color: 'rgba(255, 255, 255, 0.05)' },
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            pointLabels: { color: '#a1a1aa', font: { family: 'Inter', size: 10 } },
                            ticks: { display: false }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        const doughnutCtx = document.getElementById('adminDoughnutChart');
        if (doughnutCtx) {
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Soft Tennis', 'Cricket', 'Certificates'],
                    datasets: [{
                        data: [
                            <?php echo $tennis_count; ?>,
                            <?php echo $cricket_count; ?>,
                            <?php echo $cert_count; ?>
                        ],
                        backgroundColor: ['#00c853', '#ffd700', '#6366f1'],
                        borderColor: '#121214',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#a1a1aa',
                                font: { family: 'Inter', size: 11 },
                                boxWidth: 12
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });

    function toggleVideoSourceInput(type) {
        const uploadGroup = document.getElementById("video-upload-group");
        const urlGroup = document.getElementById("video-url-group");

        if (type === 'upload') {
            uploadGroup.style.display = 'block';
            urlGroup.style.display = 'none';
        } else {
            uploadGroup.style.display = 'none';
            urlGroup.style.display = 'block';
        }
    }
    </script>
<?php endif; ?>

</body>
</html>
