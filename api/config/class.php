<?php
include ('dbconnect.php');
date_default_timezone_set('Asia/Manila');

class global_class extends db_connect
{
    public function __construct()
    {
        $this->connect();
    }

        // Check if email exists
        public function get_vaccination_history($petId)
        {
            $query = "SELECT * FROM pets_info_history_update WHERE ph_pet_id = ? ORDER BY ph_update_at DESC";
            $stmt = $this->conn->prepare($query);
    
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                return false;
            }
    
            $stmt->bind_param("i", $petId);
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                return false;
            }
    
            $result = $stmt->get_result();
            return $result;
        }



    // Check if email exists
public function checkEmailExists($email) {
    $query = "SELECT Email FROM users WHERE Email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    return ($stmt->num_rows > 0);
}

// Store OTP with expiration
public function storeOtp($email, $otp) {
    $query = "UPDATE users SET otp_code = ? WHERE Email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ss", $otp, $email);
    return $stmt->execute();
}


public function getOtp($email) {
    $query = "SELECT otp_code FROM users WHERE Email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}


public function clearOtp($email) {
    $query = "UPDATE users SET otp_code = NULL WHERE Email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $email);
    return $stmt->execute();
}


// Update password
public function UpdatePassword($hashedPassword, $email) {
    $query = "UPDATE users SET Password = ? WHERE Email = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ss", $hashedPassword, $email);
    return $stmt->execute() ? "success" : ["error" => "Failed to update password"];
}

    
    
    
    


    public function check_account($UserID) {
      
        $query = "SELECT * FROM users WHERE UserID = $UserID";
    
        $result = $this->conn->query($query);

        // Prepare ang array para sa result
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        return $items; 
    }

    public function search_users($search) {
        $search = trim($search);

        if (!empty($search)) {
            $stmt = $this->conn->prepare("SELECT UserID, Username, Email, Role FROM users WHERE Name LIKE ? OR Username LIKE ? OR Email LIKE ?");
            $likeQuery = "%$search%";
            $stmt->bind_param("sss", $likeQuery, $likeQuery, $likeQuery);
            $stmt->execute();

            $result = $stmt->get_result();
            $users = [];

            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            return json_encode($users);
        } else {
            return json_encode([]);
        }
    }



    public function UpdateImpoundPets($imp_id, $updateNotes, $addDateCaught, $addLocationFound, $updateImpoundLocation, $updateDaysRemaining, $updatePetStatus, $petPhotoName) {
        // Start SQL query
        $sql = "UPDATE impounded_pets 
                SET imp_date_caught = ?, 
                    imp_location_found = ?, 
                    imp_location_impound = ?, 
                    imp_days_rem = ?, 
                    imp_notes = ?, 
                    imp_status = ?";
    
        // Handle imp_claim_by conditionally
        if ($updatePetStatus === "Unclaimed") {
            $sql .= ", imp_claim_by = NULL";
        }
    
        // If $petPhotoName is provided, update the photo
        if ($petPhotoName !== null) {
            $sql .= ", imp_impounded_photo = ?";
        }
    
        // Add WHERE condition
        $sql .= " WHERE imp_id = ?";
    
        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);
    
        // Bind parameters based on whether a photo is included
        if ($petPhotoName !== null) {
            $stmt->bind_param("ssssssss", $addDateCaught, $addLocationFound, $updateImpoundLocation, $updateDaysRemaining, $updateNotes, $updatePetStatus, $petPhotoName, $imp_id);
        } else {
            $stmt->bind_param("sssssss", $addDateCaught, $addLocationFound, $updateImpoundLocation, $updateDaysRemaining, $updateNotes, $updatePetStatus, $imp_id);
        }
    
        // Execute the query
        if ($stmt->execute()) {
            return "success";
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
            return ['error' => $error];
        }
    }
    
    
    
    



    public function AddImpoundPets($addDateCaught,$addLocationFound,$addImpoundLocation,$addDaysRemaining,$petPhotoName) {
        // Insert Data into Database
        $sql = "INSERT INTO impounded_pets (imp_date_caught,imp_location_found,imp_location_impound,imp_days_rem,imp_impounded_photo) VALUES (?,?,?,?,?)";
    
        $stmt = $this->conn->prepare($sql);
       
        $stmt->bind_param("sssss",$addDateCaught,$addLocationFound,$addImpoundLocation,$addDaysRemaining,$petPhotoName);
        
    
        if ($stmt->execute()) {
            return "success";
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
            return ['error' => $error];
        }
    }


    
    public function ToggleLike($UserID, $postId, $action) {
        if ($action == "like") {
            $sql = "INSERT INTO post_like (like_user_id, like_post_id, like_action) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iis", $UserID, $postId, $action);
        } else if ($action == "unlike") {
            $sql = "DELETE FROM post_like WHERE like_user_id = ? AND like_post_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $UserID, $postId);
        } else {
            return ['error' => 'Invalid action'];
        }
    
        if ($stmt->execute()) {
            return "success";
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
            return ['error' => $error];
        }
    }
    



    public function petRegistration(
        $dateApplication, $nameApplicant, $age, $gender, $birthday, $telephone,
        $emailApplicant, $homeAddress,$barangay, $petName, $petAge, $petGender, $species,
        $breed, $petWeight, $petColor, $distinguishingMarks, $petBirthday,
        $vaccinationDate, $vaccinationExpiry, $vetClinic, $vetName, $vetAddress,
        $vetContact, $dateSigned, $userPhotoName,$ValidIDName, $ownerSignatureName, $qrCodeFileName,$antiRabPic,$UserID
    ) {
        // Insert Data into Database
        $sql = "INSERT INTO pets_info (
            pet_photo_owner,pet_validIDName, pet_date_application, pet_owner_name, pet_owner_age,
            pet_owner_gender, pet_owner_birthday, pet_owner_telMobile, pet_owner_email,
            pet_owner_home_address,pet_owner_barangay, pet_name, pet_age, pet_gender, pet_species, pet_breed,
            pet_weight, pet_color, pet_marks, pet_birthday, pet_antiRabies_vac_date,
            pet_antiRabies_expi_date, pet_vet_clinic, pet_vet_name, pet_vet_clinic_address,
            pet_vet_contact_info, pet_owner_signature, pet_date_signed, pet_qr_code,pet_antiRabPic,pets_UserID
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
    
        $stmt->bind_param(
            "ssssssssssssssssssssssssssssssi", // Should match the number of placeholders in SQL (29)
            $userPhotoName, $ValidIDName, $dateApplication, $nameApplicant, $age, $gender, $birthday,
            $telephone, $emailApplicant, $homeAddress, $barangay, $petName, $petAge, $petGender,
            $species, $breed, $petWeight, $petColor, $distinguishingMarks, $petBirthday,
            $vaccinationDate, $vaccinationExpiry, $vetClinic, $vetName, $vetAddress,
            $vetContact, $ownerSignatureName, $dateSigned, $qrCodeFileName,$antiRabPic,$UserID
        );
        
    
        if ($stmt->execute()) {
            $insertedId = $this->conn->insert_id; // Get the last inserted pet_id
            $stmt->close();
            return [
                'PET ID' => $insertedId,
                'date application' => $dateApplication,
                'name applicant' => $nameApplicant,
                'age' => $age,
                'gender' => $gender,
                'birthday' => $birthday,
                'telephone' => $telephone,
                'email applicant' => $emailApplicant,
                'home address' => $homeAddress,
                'pet name' => $petName,
                'pet age' => $petAge,
                'pet gender' => $petGender,
                'species' => $species,
                'breed' => $breed,
                'pet weight' => $petWeight,
                'pet color' => $petColor,
                'distinguishing_marks' => $distinguishingMarks,
                'pet birthday' => $petBirthday,
                'vaccination_date' => $vaccinationDate,
                'vaccination_expiry' => $vaccinationExpiry,
                'vet clinic' => $vetClinic,
                'vet name' => $vetName,
                'vet address' => $vetAddress,
                'vet contact' => $vetContact,
                'date signed' => $dateSigned
            ];
        } else {
            $error = "Error: " . $stmt->error;
            $stmt->close();
            return ['error' => $error];
        }
    }
    



    public function updatePetQRCode($petID, $qrCodeFileName) {
        $updateSQL = "UPDATE pets_info SET pet_qr_code = ? WHERE pet_id = ?";
    
        // Use $this->conn instead of $db->conn
        $stmt = $this->conn->prepare($updateSQL);
        
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
    
        $stmt->bind_param("si", $qrCodeFileName, $petID);
        
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
    
        $stmt->close();
    }
    
    



    public function FetchComments($UserID, $postId) {
        // Query to fetch comments for a specific post, joining user details
        $query = "
            SELECT 
                post_comments.comments_id,
                post_comments.comments_text,
                post_comments.comments_date,
                users.Username,
                users.ProfilePic
            FROM post_comments
            LEFT JOIN users ON post_comments.comments_user_id = users.UserID
            WHERE post_comments.comments_post_id = ?
            ORDER BY post_comments.comments_date ASC
        ";
    
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            echo json_encode(['error' => 'Database error: ' . $this->conn->error]);
            return;
        }
    
        // Bind parameter (corrected)
        $stmt->bind_param("i", $postId);
        
        // Execute and fetch results
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result) {
            $comments = [];
            while ($row = $result->fetch_assoc()) {
                $comments[] = [
                    'comment_id' => $row['comments_id'],
                    'comment_text' => $row['comments_text'],
                    'comment_date' => $row['comments_date'],
                    'username' => $row['Username'],
                    'profilePic' => $row['ProfilePic']
                ];
            }
            echo json_encode($comments);
        } else {
            echo json_encode(['error' => 'Failed to retrieve comments']);
        }
    
        $stmt->close();
    }
    


    public function FetchUserPost($UserID, $offset, $limit) {
        $query = "
            SELECT post_content.*, users.Username, users.ProfilePic,
                (SELECT COUNT(*) FROM post_like WHERE post_like.like_post_id = post_content.post_id) AS like_count,
                (SELECT COUNT(*) FROM post_like WHERE post_like.like_post_id = post_content.post_id AND post_like.like_user_id = ?) AS is_liked
            FROM post_content
            LEFT JOIN users ON post_content.post_user_id = users.UserID
            WHERE post_status = '1'
            ORDER BY post_content.post_date DESC
            LIMIT ? OFFSET ?
        ";
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['error' => 'Database error: ' . $this->conn->error];
        }
    
        $stmt->bind_param("iii", $UserID, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $row['is_liked'] = ($row['is_liked'] > 0) ? true : false;
            $posts[] = $row;
        }
    
        $stmt->close();
        return $posts;
    }
    


    public function FetchAllUsers($UserID) {
        $query = "
            SELECT 
                users.UserID, 
                users.Username, 
                COALESCE(SUM(CASE WHEN chat_messages.message_seen = 0 AND chat_messages.receiver_id = ? THEN 1 ELSE 0 END), 0) AS unseen_messages
            FROM users
            LEFT JOIN chat_messages ON users.UserID = chat_messages.sender_id
            WHERE users.UserID != ?
            GROUP BY users.UserID
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $UserID, $UserID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
    
        echo json_encode($users);
    }
    


    public function fetchUserChats($sender_id, $receiver_id) {


        // First, update message_seen for received messages
        $updateSql = "UPDATE chat_messages 
        SET message_seen = 1 
        WHERE sender_id = ? AND receiver_id = ?";

        $updateStmt = $this->conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $receiver_id, $sender_id);
        $updateStmt->execute();
        $updateStmt->close();





        $sql = "SELECT cm.*, 
                       sender.UserID AS sender_id, sender.Name AS sender_name, sender.ProfilePic AS sender_profile, 
                       receiver.UserID AS receiver_id, receiver.Name AS receiver_name, receiver.ProfilePic AS receiver_profile
                FROM chat_messages cm
                LEFT JOIN users sender ON sender.UserID = cm.sender_id
                LEFT JOIN users receiver ON receiver.UserID = cm.receiver_id
                WHERE (cm.sender_id = ? AND cm.receiver_id = ?) 
                   OR (cm.sender_id = ? AND cm.receiver_id = ?)
                ORDER BY cm.chat_id ASC";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    
        return $messages;
    }
    
    
    



    public function UpdateProfile($profilePicName, $email, $owner_name, $username, $gender, $birthdate, $contact, $address, $link_address, $UserID,$bio)
    {
        // Start constructing the SQL statement
        $sql = "UPDATE users SET Email = ?, Name = ?, Username = ?, Gender = ?, BirthDate = ?, Contact = ?, Address = ?, Link_address = ?,Bio=?";
        
        // Array to store the parameters and their types
        $params = [$email, $owner_name, $username, $gender, $birthdate, $contact, $address, $link_address,$bio];
        $types = "sssssssss"; // Corresponding types for bind_param
    
        // Include ProfilePic in the update only if it's not null
        if ($profilePicName !== null) {
            $sql .= ", ProfilePic = ?";
            $params[] = $profilePicName;
            $types .= "s"; // Add one more string type
        }
    
        // Add the WHERE condition
        $sql .= " WHERE UserID = ?";
        $params[] = $UserID;
        $types .= "i"; // UserID is an integer
    
        // Prepare SQL statement
        $stmt = $this->conn->prepare($sql);
    
        if (!$stmt) {
            return json_encode(array('status' => 'error', 'message' => 'Failed to prepare statement.'));
        }
    
        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);
    
        // Execute the query
        if ($stmt->execute()) {
            $stmt->close();
            return json_encode(array('status' => 'success', 'message' => 'Profile updated successfully.'));
        } else {
            $stmt->close();
            return json_encode(array('status' => 'error', 'message' => 'Unable to update profile.'));
        }
    }
    





    public function UpdatePetStatus($pet_id, $status)
    {
        // Corrected SQL query with UPDATE keyword and placeholders
        $stmt = $this->conn->prepare("UPDATE `pets_info` SET `pet_status` = ? WHERE `pet_id` = ?");
        
        // Corrected bind_param order: "si" (string, integer)
        $stmt->bind_param("si", $status, $pet_id);
        
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Unable to update'
            );
        }
        
        // Close statement
        $stmt->close();
    
        // Send JSON response
        echo json_encode($response);
    }
    

    public function ClaimPet($imp_id,$UserID)
    {
        
            // Update query excluding post_images
            $stmt = $this->conn->prepare("UPDATE `impounded_pets` SET `imp_claim_by` = ? , `imp_status`='Pending' WHERE `imp_id` = ?");
            $stmt->bind_param("ss",$UserID,$imp_id);
        
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
            echo json_encode($response);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Unable to delete post'));
        }
    }



    public function updatePetInfo($pet_id, $vaccine_given, $vaccine_due)
{
    // Fetch the previous vaccination dates before updating
    $query = $this->conn->prepare("SELECT pet_antiRabies_vac_date, pet_antiRabies_expi_date FROM pets_info WHERE pet_id = ?");
    $query->bind_param("s", $pet_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $prev_vac_date = $row['pet_antiRabies_vac_date'];
        $prev_expi_date = $row['pet_antiRabies_expi_date'];

        // Insert into history table
        $history_stmt = $this->conn->prepare("INSERT INTO pets_info_history_update (ph_pet_id, ph_pet_antiRabies_vac_date, ph_pet_antiRabies_expi_date, ph_update_at) VALUES (?, ?, ?, NOW())");
        $history_stmt->bind_param("sss", $pet_id, $prev_vac_date, $prev_expi_date);
        $history_stmt->execute();
    }

    // Update pet's vaccine records
    $stmt = $this->conn->prepare("UPDATE pets_info SET pet_antiRabies_expi_date = ?, pet_antiRabies_vac_date = ? WHERE pet_id = ?");
    $stmt->bind_param("sss", $vaccine_due, $vaccine_given, $pet_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        error_log("Update error: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Unable to update']);
    }
}







    public function VerifiedVet($vet_id,$status)
    {
        
            // Update query excluding post_images
            $stmt = $this->conn->prepare("UPDATE `users` SET `status` = ? WHERE `UserID` = ?");
            $stmt->bind_param("ii",$status,$vet_id);
        
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
            echo json_encode($response);
        } 
    }






    public function DeletePost($deletepostid)
    {
        
            // Update query excluding post_images
            $stmt = $this->conn->prepare("UPDATE `post_content` SET `post_status` = '0' WHERE `post_id` = ?");
            $stmt->bind_param("s",$deletepostid);
        
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
            echo json_encode($response);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Unable to delete post'));
        }
    }

    public function deleteImpound($imp_id)
    {
        // Delete query for impound
        $stmt = $this->conn->prepare("DELETE FROM `impounded_pets` WHERE `imp_id` = ?");
        $stmt->bind_param("s", $imp_id);
        
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
            echo json_encode($imp_id);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Unable to delete post'));
        }
    }



    public function EditPost($editpostid, $postInput, $postFilesJson)
    {
        if (!empty($postFilesJson)) {
            // Update query including post_images
            $stmt = $this->conn->prepare("UPDATE `post_content` SET `post_content` = ?, `post_images` = ? WHERE `post_id` = ?");
            $stmt->bind_param("sss", $postInput, $postFilesJson, $editpostid);
        } else {
            // Update query excluding post_images
            $stmt = $this->conn->prepare("UPDATE `post_content` SET `post_content` = ? WHERE `post_id` = ?");
            $stmt->bind_param("ss", $postInput, $editpostid);
        }
    
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
            echo json_encode($response);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Unable to update post'));
        }
    }



    public function PostContent($post_user_id,$postInput, $postFilesJson)
    {
        // Proceed with insertion if email does not exist
        $stmt = $this->conn->prepare("INSERT INTO `post_content` (`post_user_id`, `post_content`, `post_images`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss",$post_user_id, $postInput,$postFilesJson);
    
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
            echo json_encode($response);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Unable to post'));
        }
    }


    public function SentMessagge($sender_id, $reciever_id, $message, $uniqueFileName)
    {
        $stmt = $this->conn->prepare("INSERT INTO `chat_messages` (`sender_id`, `receiver_id`, `message_text`, `message_media`) VALUES (?, ?, ?, ?)");
    
        if (!$stmt) {
            return json_encode(array('status' => 'error', 'message' => 'SQL prepare failed: ' . $this->conn->error));
        }
    
        $stmt->bind_param("ssss", $sender_id, $reciever_id, $message, $uniqueFileName);
    
        if ($stmt->execute()) {
            return json_encode(array('status' => 'success'));
        } else {
            return json_encode(array('status' => 'error', 'message' => 'Unable to post: ' . $stmt->error));
        }
    }
    
    
    
    

    

    
    public function fetch_impound_pets()
    {
        $query = $this->conn->prepare("SELECT * from impounded_pets ");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }


    public function fetch_pending_pets($status)
    {
        $query = $this->conn->prepare("SELECT * from pets_info where pet_status='$status' ");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }


    public function fetch_user($role)
    {
        $query = $this->conn->prepare("SELECT * from users where `Role`='$role' AND `status`!='2'");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }



    public function fetch_all_vet()
    {
        $query = $this->conn->prepare("SELECT * from users where Role='vet' ORDER BY `UserID` DESC ");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }


    public function getNotificationCount($UserID)
    {
        // Query for expiring and expired vaccinations
        $query = $this->conn->prepare("
            SELECT  
                COUNT(CASE WHEN DATEDIFF(pet_antiRabies_expi_date, CURDATE()) BETWEEN 0 AND 3 THEN 1 END) AS total_soon_to_expi,
                COUNT(CASE WHEN pet_antiRabies_expi_date < CURDATE() THEN 1 END) AS totalexpi
            FROM pets_info
            WHERE pets_UserID = ?
        ");
        
        $query->bind_param("i", $UserID);
    
        if ($query->execute()) {
            $result = $query->get_result()->fetch_assoc();
        } else {
            echo json_encode(["error" => "Query execution failed"]);
            return;
        }
    
        // Get pet names and expiration dates for soon-to-expire vaccinations
        $query2 = $this->conn->prepare("
            SELECT pet_name, pet_antiRabies_expi_date
            FROM pets_info 
            WHERE pets_UserID = ? AND DATEDIFF(pet_antiRabies_expi_date, CURDATE()) BETWEEN 0 AND 3
        ");
        
        $query2->bind_param("i", $UserID);
        $soon_to_expire_pets = [];
    
        if ($query2->execute()) {
            $result2 = $query2->get_result();
            while ($row = $result2->fetch_assoc()) {
                $soon_to_expire_pets[] = [
                    "name" => $row['pet_name'],
                    "expiry_date" => $row['pet_antiRabies_expi_date']
                ];
            }
        }
    
        // Get pet names for expired vaccinations
        $query3 = $this->conn->prepare("
            SELECT pet_name 
            FROM pets_info 
            WHERE pets_UserID = ? AND pet_antiRabies_expi_date < CURDATE()
        ");
        
        $query3->bind_param("i", $UserID);
        $expired_pets = [];
    
        if ($query3->execute()) {
            $result3 = $query3->get_result();
            while ($row = $result3->fetch_assoc()) {
                $expired_pets[] = $row['pet_name'];
            }
        }
    
        // Query to count unseen messages
        $query4 = $this->conn->prepare("
            SELECT COUNT(*) AS unseen_messages
            FROM chat_messages
            WHERE receiver_id = ? AND message_seen = 0
        ");
    
        $query4->bind_param("i", $UserID);
        $unseen_messages = 0;
    
        if ($query4->execute()) {
            $result4 = $query4->get_result()->fetch_assoc();
            $unseen_messages = $result4['unseen_messages'];
        }
    
        // Combine results
        echo json_encode([
            "total_soon_to_expi" => $result['total_soon_to_expi'],
            "totalexpi" => $result['totalexpi'],
            "total_notifications" => (int)$result['total_soon_to_expi'] + (int)$result['totalexpi'] + (int)$unseen_messages,
            "soon_to_expire_pets" => $soon_to_expire_pets,
            "expired_pets" => $expired_pets,
            "unseen_messages" => $unseen_messages
        ]);
    }
    



    public function getAllNotificationCount($UserID)
    {
        // Query for expiring and expired vaccinations
        $query = $this->conn->prepare("
            SELECT  
                COUNT(CASE WHEN DATEDIFF(pet_antiRabies_expi_date, CURDATE()) BETWEEN 0 AND 3 THEN 1 END) AS total_soon_to_expi,
                COUNT(CASE WHEN pet_antiRabies_expi_date < CURDATE() THEN 1 END) AS totalexpi
            FROM pets_info
        ");
    
        if (!$query->execute()) {
            echo json_encode(["error" => "Query execution failed"]);
            return;
        }
    
        $result = $query->get_result()->fetch_assoc();
    
        // Get pet names and expiration dates for soon-to-expire vaccinations
        $query2 = $this->conn->prepare("
            SELECT pet_name, pet_antiRabies_expi_date
            FROM pets_info 
            WHERE DATEDIFF(pet_antiRabies_expi_date, CURDATE()) BETWEEN 0 AND 3
        ");
    
        $soon_to_expire_pets = [];
    
        if ($query2->execute()) {
            $result2 = $query2->get_result();
            while ($row = $result2->fetch_assoc()) {
                $soon_to_expire_pets[] = [
                    "name" => $row['pet_name'],
                    "expiry_date" => $row['pet_antiRabies_expi_date']
                ];
            }
        }
    
        // Get pet names for expired vaccinations
        $query3 = $this->conn->prepare("
            SELECT pet_name 
            FROM pets_info 
            WHERE pet_antiRabies_expi_date < CURDATE()
        ");
    
        $expired_pets = [];
    
        if ($query3->execute()) {
            $result3 = $query3->get_result();
            while ($row = $result3->fetch_assoc()) {
                $expired_pets[] = $row['pet_name'];
            }
        }
    
        // Query to count all unseen messages
        $query4 = $this->conn->prepare("
            SELECT COUNT(*) AS unseen_messages
            FROM chat_messages
            WHERE message_seen = 0 AND receiver_id=$UserID
        ");
    
        $unseen_messages = 0;
    
        if ($query4->execute()) {
            $result4 = $query4->get_result()->fetch_assoc();
            $unseen_messages = $result4['unseen_messages'];
        }
    
        // Combine results
        echo json_encode([
            "total_soon_to_expi" => $result['total_soon_to_expi'],
            "totalexpi" => $result['totalexpi'],
            "total_notifications" => (int)$result['total_soon_to_expi'] + (int)$result['totalexpi'] + (int)$unseen_messages,
            "soon_to_expire_pets" => $soon_to_expire_pets,
            "expired_pets" => $expired_pets,
            "unseen_messages" => $unseen_messages
        ]);
    }
    



    
    


    public function fetch_pets_info($UserID)
    {
        $query = $this->conn->prepare("SELECT * from pets_info where pets_UserID='$UserID' ORDER BY `pet_id` DESC");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }



    public function fetch_all_pets_info()
    {
        $query = $this->conn->prepare("SELECT * from pets_info where pet_status ='accept_by_lgu' OR pet_status ='declined_by_lgu' OR pet_status ='declined_by_vet' OR pet_status ='accept_by_vet'");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }


    public function fetch_lgu_registered_pet()
    {
        $query = $this->conn->prepare("SELECT * from pets_info where pet_status ='accept_by_lgu' OR pet_status ='declined_by_lgu'");

        if ($query->execute()) {
            $result = $query->get_result();
            return $result;
        }
    }
    


    public function AddComment($UserID, $postId, $commentText)
    {
        // Proceed with insertion if email does not exist
        $stmt = $this->conn->prepare("INSERT INTO `post_comments` (`comments_user_id`,`comments_post_id`,`comments_text`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss",$UserID, $postId,$commentText);
    
        if ($stmt->execute()) {
            $response = array(
                'status' => 'success'
            );
            echo json_encode($response);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Unable to sent'));
        }
    }




    public function SignUp($email, $username, $password, $role, $license_proof)
{
    // Check if the email already exists
    $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `Email` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(array('status' => 'email_already', 'message' => 'Email already exists'));
        return;
    }

    // Check if the username already exists
    $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `Username` = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(array('status' => 'username_already', 'message' => 'Username already exists'));
        return;
    }

    // Hash the password using SHA-256
    $hashedPassword = hash('sha256', $password);

    if($role=='vet'){
        $stmt = $this->conn->prepare("INSERT INTO `users` (`Username`, `Email`, `Password`, `Role`, `license_proof`,`status`) VALUES (?, ?, ?, ?, ?,'0')");
    }else{
        $stmt = $this->conn->prepare("INSERT INTO `users` (`Username`, `Email`, `Password`, `Role`, `license_proof`,`status`) VALUES (?, ?, ?, ?, ?,'1')");
    }
    
    
    // Bind parameters (use "sssss" because all fields are strings)
    $stmt->bind_param("sssss", $username, $email, $hashedPassword, $role, $license_proof);

    if ($stmt->execute()) {
        session_start();
        $userId = $this->conn->insert_id;

        $response = array(
            'status' => 'success',
            'id' => $userId
        );
        echo json_encode($response);
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Unable to register'));
    }
}




public function UpdatelguAccount($lguId, $username, $fullName, $email, $address, $password)
{
    // Initialize base query
    $query = "UPDATE `users` SET `Name` = ?, `Username` = ?, `Email` = ?, `Address` = ? ";
    $params = [$fullName, $username, $email, $address]; // Initial parameters

    // Check if password is provided
    if (!empty($password)) {
        $hashedPassword = hash('sha256', $password);
        $query .= ", `Password` = ? ";
        $params[] = $hashedPassword;
    }

    $query .= "WHERE `UserID` = ?";
    $params[] = $lguId;

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    if (!$stmt) {
        die(json_encode(array('status' => 'error', 'message' => 'Database error: ' . $this->conn->error)));
    }

    // Bind parameters dynamically
    $types = str_repeat("s", count($params) - 1) . "i"; // Generate types dynamically (last parameter is integer)
    $stmt->bind_param($types, ...$params);

    // Execute and check
    if ($stmt->execute()) {
        echo json_encode(array('status' => 'success', 'message' => 'Account updated successfully'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Unable to update account'));
    }

    $stmt->close();
}




public function DeletelguAccount($lguId)
{
    // Prepare the UPDATE query
    $stmt = $this->conn->prepare("UPDATE `users` SET `status` = '2' WHERE `UserID` = ?");

    $stmt->bind_param("i",$lguId);

    if ($stmt->execute()) {
        echo json_encode(array('status' => 'success', 'message' => 'Account updated successfully'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Unable to update account'));
    }

    $stmt->close();
}





public function AddlguAccount($username, $fullName, $email, $address, $password)
{
    // Check if the email already exists
    $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `Email` = ?");
    if (!$stmt) {
        die(json_encode(array('status' => 'error', 'message' => 'Database error: ' . $this->conn->error)));
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(array('status' => 'email_already', 'message' => 'Email already exists'));
        $stmt->close();
        return;
    }
    $stmt->close();

    // Check if the username already exists
    $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE `Username` = ?");
    if (!$stmt) {
        die(json_encode(array('status' => 'error', 'message' => 'Database error: ' . $this->conn->error)));
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(array('status' => 'username_already', 'message' => 'Username already exists'));
        $stmt->close();
        return;
    }
    $stmt->close();

    // Hash the password using SHA-256
    $hashedPassword = hash('sha256', $password);

    // Insert the new user into the database
    $stmt = $this->conn->prepare("INSERT INTO `users` (`Name`, `Username`, `Email`, `Address`, `Password`, `Role`, `status`) VALUES (?, ?, ?, ?, ?, ?, '1')");
    
    if (!$stmt) {
        die(json_encode(array('status' => 'error', 'message' => 'Database error: ' . $this->conn->error)));
    }

    $role = "lgu"; // Define role separately
    $stmt->bind_param("ssssss", $fullName, $username, $email, $address, $hashedPassword, $role);

    if ($stmt->execute()) {
        $userId = $this->conn->insert_id;
        $stmt->close();


        echo json_encode(array('status' => 'success', 'id' => $userId));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Unable to create account'));
        $stmt->close();
    }
}


    



    public function LogIn($username, $password)
{
    // Hash the input password using SHA-256
    $hashedPassword = hash('sha256', $password);
    
    // Prepare the SQL query
    $query = $this->conn->prepare("SELECT * FROM `users` WHERE `Username` = ? AND `Password` = ? AND `status` != '2'");
    
    // Bind the username and the hashed password
    $query->bind_param("ss", $username, $hashedPassword);
    
    // Execute the query
    if ($query->execute()) {
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            session_start();
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['Role'] = $user['Role'];
            
            // Return user data along with role
            return [
                'UserID' => $user['UserID'],
                'Username' => $user['Username'],
                'Role' => $user['Role']
            ];
        } else {
            return false; // No user found
        }
    } else {
        return false; // Query execution failed
    }
}



   










}