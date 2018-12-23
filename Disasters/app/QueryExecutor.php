<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QueryExecutor extends Model
{
  private $conn;

  function __construct()
  {
    parent::__construct();

    $this->conn = mysqli_connect("localhost", "root", "", "Disasters");

    if (!$this->conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
  }

  public function addReport($description, $ssn)
  {
    $sql = "Insert into Report (content, citizen_ssn) values ('" . $description . "', '".$ssn."')"; //User SSN s is to be taken from the cookie once log in is done

    if (mysqli_query($this->conn, $sql)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
    }
  }

  public function getIncID()
  {
    $sql = "Select id From incident";

    if (mysqli_query($this->conn, $sql)) {
        $data = mysqli_query($this->conn, $sql);
        return ($data);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
        return;
    }
  }

  public function addDisc($inc_id, $question, $ssn)
  {
    $sql = "Insert into discussion (question, incident_id,  citizen_ssn) values ('".$question."', ".$inc_id.", '".$ssn."')"; //User SSN s is to be taken from the cookie once log in is done

    if (mysqli_query($this->conn, $sql)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
    }
  }

  public function getCitizenCred($username)
  {
    $sql = "Select ssn, username, password From citizen Where username = '".$username."'";
    if (mysqli_query($this->conn, $sql)) {
        $data = mysqli_query($this->conn, $sql);
        return ($data);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
        return;
    }
  }

  public function getGovRepCred($username)
  {
    $sql = "Select ssn, username, password From government_representative Where username = '".$username."'";
    if (mysqli_query($this->conn, $sql)) {
        $data = mysqli_query($this->conn, $sql);
        return ($data);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
        return;
    }
  }

  public function getAdminCred($username)
  {
    $sql = "Select ssn, username, password From admin Where username = '".$username."'";
    if (mysqli_query($this->conn, $sql)) {
        $data = mysqli_query($this->conn, $sql);
        return ($data);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
        return;
    }
  }

  public function getALLSSN()
  {
    $sql = "Select ssn From person";
    if (mysqli_query($this->conn, $sql)) {
        $data = mysqli_query($this->conn, $sql);
        return ($data);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
        return;
    }
  }

  public function addCitizen($ssn, $name, $username, $password, $age, $address, $gender)
  {
    $sql1 = "Insert into person (ssn, name, age, gender, address) values ('".$ssn."', '".$name."', ".$age.", ".$gender.", ".$address.")";
    $sql2 = "Insert into Citizen (ssn, username, password) values ('".$ssn."', '".$username."', '".$password."')";
    if (mysqli_query($this->conn, $sql1)) {
      if (mysqli_query($this->conn, $sql2))
      {
        echo "New record created successfully";
      }
      else
      {
        echo "Error: " . $sql2 . "<br>" . mysqli_error($this->conn);
      }
    } else {
        echo "Error: " . $sql1 . "<br>" . mysqli_error($this->conn);
    }
  }

  public function getPassword($ssn)
  {
     $sql = "Select password From citizen Where ssn = '".$ssn."' Union Select password From admin Where ssn = '".$ssn.
            "' Union Select password From government_representative Where ssn = '".$ssn."'";
     if (mysqli_query($this->conn, $sql)) {
         $data = mysqli_query($this->conn, $sql);
         return ($data);
     } else {
         echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
         return;
     }
  }

  public function updatePassword($ssn, $new_password)
  {
    $sql= "Select password From citizen Where ssn = '".$ssn."'";
    if (mysqli_num_rows(mysqli_query($this->conn, $sql)) != 0)
    {
      $sql = "Update citizen Set password = '".$new_password."' Where ssn = '".$ssn."'";
      if (mysqli_query($this->conn, $sql)) {
          echo "Password Updated Successfully";
      } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
      }
    }
    else
    {
      $sql= "Select password From government_representative Where ssn = '".$ssn."'";
      if (mysqli_num_rows(mysqli_query($this->conn, $sql)) != 0)
      {
        $sql = "Update government_representative Set password = '".$new_password."' Where ssn = '".$ssn."'";
        if (mysqli_query($this->conn, $sql)) {
            echo "Password Updated Successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
        }
    }
    else
    {
      $sql= "Select password From admin Where ssn = '".$ssn."'";
      if (mysqli_num_rows(mysqli_query($this->conn, $sql)) != 0)
      {
        $sql = "Update admin Set password = '".$new_password."' Where ssn = '".$ssn."'";
        if (mysqli_query($this->conn, $sql)) {
            echo "Password Updated Successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
        }
        }
      }
    }
  }

  public function GetReports()
  {
      $sql = "Select report_id, content From report";
      if (mysqli_query($this->conn, $sql)) {
          $data = mysqli_query($this->conn, $sql);
          return ($data);
      } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
          return;
      }
  }

  public function TrustReport($id, $gov_ssn)
  {
      $sql = "Update report Set govn_ssn = '".$gov_ssn."' Where report_id = ".$id;
      if (mysqli_query($this->conn, $sql)) {
          echo "Updated successfully";
          $sql = "Select citizen_ssn From report where report_id = ".$id;
          if (mysqli_query($this->conn, $sql)) {
              $data = mysqli_query($this->conn, $sql);
              $data = mysqli_fetch_assoc($data);
              $data = $data['citizen_ssn'];
              $sql = "Update Citizen Set trust_level = trust_level + 1 Where ssn = '".$data."'";
              if (mysqli_query($this->conn, $sql)) {
                  echo "Trust Level Increased";
              } else {
                  echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
                  return;
              }
          } else {
              echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
              return;
          }
      } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
      }
  }

  function __destruct()
  {
    mysqli_close($this->conn);
  }
}
