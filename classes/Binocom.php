<?php

require_once "Db.php";

class Binocom Extends Db{
    private $dbconn;

    public function __construct(){
        $this->dbconn = $this->connect();
    }

    // EXAMPLE METHOD PRESERVED
    public function checkClaim($student_id, $founditems_id){
        try{
            $sql = "SELECT * FROM claims WHERE student_id = ? AND founditems_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$student_id, $founditems_id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return false;
        }
    }

    // Get all LGAs for a given state (Default state id: 25 for Delta State)
    public function getLgas($state_id = 25){
        try{
            $sql = "SELECT * FROM lga WHERE state_id = ? ORDER BY lga_name ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$state_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Get single LGA details by lga_id
    public function getLgaDetails($lga_id){
        try{
            $sql = "SELECT * FROM lga WHERE lga_id = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$lga_id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return false;
        }
    }

    // Get Wards under a specific LGA
    public function getWardsByLga($lga_id){
        try{
            $sql = "SELECT * FROM ward WHERE lga_id = ? ORDER BY ward_name ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$lga_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Get Polling Units under a specific LGA and Ward
    public function getPollingUnitsByWard($lga_id, $ward_id){
        try{
            $sql = "SELECT * FROM polling_unit WHERE lga_id = ? AND ward_id = ? ORDER BY polling_unit_name ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$lga_id, $ward_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Get Polling Units under a specific LGA
    public function getPollingUnitsByLga($lga_id){
        try{
            $sql = "SELECT * FROM polling_unit WHERE lga_id = ? ORDER BY polling_unit_name ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$lga_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Get all Polling Units
    public function getAllPollingUnits(){
        try{
            $sql = "SELECT pu.*, l.lga_name, w.ward_name 
                    FROM polling_unit pu 
                    LEFT JOIN lga l ON pu.lga_id = l.lga_id 
                    LEFT JOIN ward w ON pu.lga_id = w.lga_id AND pu.ward_id = w.ward_id 
                    ORDER BY pu.polling_unit_name ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Get Polling Unit Details by its uniqueid
    public function getPollingUnitDetails($pu_uniqueid){
        try{
            $sql = "SELECT pu.*, l.lga_name, w.ward_name 
                    FROM polling_unit pu 
                    LEFT JOIN lga l ON pu.lga_id = l.lga_id 
                    LEFT JOIN ward w ON pu.lga_id = w.lga_id AND pu.ward_id = w.ward_id 
                    WHERE pu.uniqueid = ?";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$pu_uniqueid]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return false;
        }
    }

    // Get Announced Results for an individual Polling Unit
    public function getPollingUnitResults($pu_uniqueid){
        try{
            $sql = "SELECT * FROM announced_pu_results WHERE polling_unit_uniqueid = ? ORDER BY party_abbreviation ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$pu_uniqueid]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Calculate Summed Total Results of all Polling Units under a particular LGA (Question 2 core logic)
    public function getLgaSummedResults($lga_id){
        try{
            $sql = "SELECT r.party_abbreviation, SUM(r.party_score) as total_score 
                    FROM announced_pu_results r 
                    INNER JOIN polling_unit pu ON r.polling_unit_uniqueid = pu.uniqueid 
                    WHERE pu.lga_id = ? 
                    GROUP BY r.party_abbreviation 
                    ORDER BY total_score DESC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$lga_id]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Get Official Announced LGA Results (used for optional cross-checking/comparison)
    public function getAnnouncedLgaResults($lga_name){
        try{
            $sql = "SELECT * FROM announced_lga_results WHERE lga_name = ? ORDER BY party_abbreviation ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute([$lga_name]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Get list of all political parties from party table
    public function getAllParties(){
        try{
            $sql = "SELECT * FROM party ORDER BY partyname ASC";
            $stmt = $this->dbconn->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;
        }catch(PDOException $e){
            return [];
        }
    }

    // Store new polling unit and its results for ALL parties (Question 3 core logic)
    public function addPollingUnitWithResults($pu_data, $party_scores){
        try{
            $this->dbconn->beginTransaction();

            // Insert into polling_unit table
            $sql_pu = "INSERT INTO polling_unit (polling_unit_id, ward_id, lga_id, uniquewardid, polling_unit_number, polling_unit_name, polling_unit_description, lat, `long`, entered_by_user, date_entered, user_ip_address) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
            $stmt_pu = $this->dbconn->prepare($sql_pu);
            $stmt_pu->execute([
                $pu_data['polling_unit_id'],
                $pu_data['ward_id'],
                $pu_data['lga_id'],
                $pu_data['uniquewardid'] ?? null,
                $pu_data['polling_unit_number'],
                $pu_data['polling_unit_name'],
                $pu_data['polling_unit_description'] ?? '',
                $pu_data['lat'] ?? '',
                $pu_data['long'] ?? '',
                $pu_data['entered_by_user'],
                $pu_data['user_ip_address']
            ]);

            $new_pu_uniqueid = $this->dbconn->lastInsertId();

            // Insert results for each party into announced_pu_results table
            $sql_result = "INSERT INTO announced_pu_results (polling_unit_uniqueid, party_abbreviation, party_score, entered_by_user, date_entered, user_ip_address) 
                           VALUES (?, ?, ?, ?, NOW(), ?)";
            $stmt_result = $this->dbconn->prepare($sql_result);

            foreach($party_scores as $party_abbr => $score){
                $stmt_result->execute([
                    $new_pu_uniqueid,
                    $party_abbr,
                    (int)$score,
                    $pu_data['entered_by_user'],
                    $pu_data['user_ip_address']
                ]);
            }

            $this->dbconn->commit();
            return $new_pu_uniqueid;
        }catch(PDOException $e){
            if($this->dbconn->inTransaction()){
                $this->dbconn->rollBack();
            }
            return false;
        }
    }
}

?>