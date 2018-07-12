<?php
namespace App\Service;

use App\Entity\Student;
use App\Entity\College;
use App\Entity\Major;
use App\Entity\Permission;

class MoveHereService
{
    private $entityManager;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function moveHere()
    {
        $studentDb = $this->entityManager->getRepository(Student::class);
        $collegeDb = $this->entityManager->getRepository(College::class);
        $majorDb = $this->entityManager->getRepository(Major::class);
        $permissionDb = $this->entityManager->getRepository(Permission::class);
        $db = mysqli_connect('localhost:3306', 'root', 'GT338570');
        mysqli_select_db($db, 'nefuer');
        mysqli_query($db,'set names utf8');
        $sql = 'SELECT `u`.`acc`, `u`.`pwd`, `u`.`name`, `u`.`sex`, `c`.`name`, `m`.`name`, `w`.`openid`, `w`.`create_time`, `u`.`last_upd`
            FROM `user` `u`
            LEFT JOIN `wx_user` `w`
            ON `u`.`acc` = `w`.`acc`
            LEFT JOIN `college` `c`
            ON `u`.`college_id` = `c`.`id`
            LEFT JOIN `major` `m`
            ON `u`.`major_id` = `m`.`id`
        ';
        $result = mysqli_query($db, $sql);
        $students = array();
        $permits = array();
        $accs = array();
        for($i = 0, $len = mysqli_num_rows($result); $i < $len; $i++)
        {
            $user = mysqli_fetch_row($result);
            if (in_array($user[0], $accs)) {
                continue;
            }
            if (empty($user[4]) || empty($user[5])) {
                continue;
            }
            $accs[] = $user[0];
            $student = array(
                'account' => $user[0], 
                'password' => $user[1],
                'name' => $user[2],
                'sex' => $user[3],
                'openid' => $user[6] ?? '',
                'grade' => (int)substr($user[0], 0, 4),
            );
            if ( ! empty($user[7])) {
                $student['created'] = new \DateTime(date('Y-m-d H:i:s', $user[7]), new \DateTimeZone('PRC'));
            }
            $collegeId = $collegeDb->getId($user[4] . '学院');
            $student['majorId'] = $majorDb->getId($user[5], $collegeId);
            $students[] = $student;
            $permit = $user['8'] != -1;
            $permits[] = array(
                'name' => '成绩',
                'account' => $user[0],
                'permit' => $permit,
            );
            $permits[] = array(
                'name' => '阶段成绩',
                'account' => $user[0],
                'permit' => true,
            );
            $permits[] = array(
                'name' => '考试',
                'account' => $user[0],
                'permit' => $permit,
            );
            if (count($students) > 1000) {
                $studentDb->insert($students);
                $permissionDb->insert($permits);
                $students = array();
                $permits = array();
            }
        }
        $studentDb->insert($students);
        $permissionDb->insert($permits);
    }
}