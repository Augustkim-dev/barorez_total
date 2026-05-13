<?php
$_SERVER['DOCUMENT_ROOT'] = "/data/wwwroot/ur4rest.kr";
include $_SERVER['DOCUMENT_ROOT']."/lib_inc.php";


$type='basic';
$to_idx = 8484;

test_score_lmsi($to_idx);
test_report_lmsi($to_idx);
function test_score_lmsi($to_idx, $type='basic')
{
    global $DB;
    if ($to_idx > 0) {
        $row = $DB->fetch_assoc("select * from test_to_t where idx=".$to_idx);
        $set = array();
        $set['mt_idx'] = $row['mt_idx'];
        $set['pt_idx'] = $row['pt_idx'];
        $set['pdt_idx'] = $row['pdt_idx'];
        $set['tt_idx'] = $row['tt_idx'];
        $set['to_idx'] = $row['idx'];
        $set['ttt_name'] = $row['ttt_name'];
        if ($row) {
            $table_name = "test_category_t";
            $field_name = "tct";
            if ($type=='guardian') {
                $table_name = "test_guardian_t";	//보호자
                $field_name = "tgt";
            }
            if ($type=='teacher') {
                $table_name = "test_teacher_t";	//상담사
                $field_name = "ttt";
            }
            if ($type=='self') {
                $table_name = "test_self_t";	//SELF
                $field_name = "tst";
            }
            if ($type=='professor') {
                $table_name = "test_professor_t";	//교수
                $field_name = "tpt";
            }
            $calc = $DB->select_query("select * from ".$table_name." where pt_idx=".$row['pt_idx']." and pdt_idx=".$row['pdt_idx']);


            //카테고리
            $dev_arr = array();
            foreach ($calc as $val) {
                if ($field_name == 'tct' && $val['pt_idx']=='4' && $val['tct_type_order']=='3' && $val['tct_reference']) {
                    continue;
                }	//올리닻 진로 흥미
                if ($val[$field_name.'_reference'] && $val['pt_idx']!='22') {	//참조 문항이 있을경우, 직무캐릭터가 아니면서

                    $class_arr = explode(',', $val[$field_name.'_reference']);
                    $score_plus = '';	//카테고리별 점수 예:3,4,2,1
                    //print_R($class_arr);
                    foreach ($class_arr as $class_val) {
                        $take_to = $DB->fetch_assoc("select GROUP_CONCAT(ttt_select) as score from test_take_t where ttt_idx='".$row['idx']."' and ttt_class='".trim($class_val)."' group by ttt_class");
                        //echo "select GROUP_CONCAT(ttt_select) as score from test_take_t where ttt_idx='".$row['idx']."' and ttt_class='".trim($class_val)."' group by ttt_class"."<br>";
                        if ($take_to['score']) {
                            if ($score_plus) {
                                $score_plus = $score_plus.','.$take_to['score'];
                            } else {
                                $score_plus = $take_to['score'];
                            }
                        }
                    }
                    $score_arr = explode(',', $score_plus);
                } else {
                    $take_to = $DB->fetch_assoc("select GROUP_CONCAT(ttt_select) as score from test_take_t where ttt_idx='".$row['idx']."' and ttt_class='".$val[$field_name.'_name']."' group by ttt_class");
                    echo "select GROUP_CONCAT(ttt_select) as score from test_take_t where ttt_idx='".$row['idx']."' and ttt_class='".$val[$field_name.'_name']."' group by ttt_class<br>";
                    $score_arr = explode(',', $take_to['score']);
                }
                $dev = 0;
                //if($score_arr[0] > 0 || $val['pt_idx']=='22'){	//pt_idx=22 는 ccci
                $dev = array_sum($score_arr);				//카테고리별 합
                $dev_count = count($score_arr);
                $dev_arr[$val[$field_name.'_name']] = $dev.','.$dev_count;
                //}
            }

            $continuity = 0;
            $take_to = $DB->select_query("select * from test_take_t where ttt_idx='".$row['idx']."' order by tat_num asc");
            foreach ($take_to as $val) {
                $take_arr[$val['tat_num']] = $val['ttt_select'];
            }

            //연속 응답 1번~15번 문항 선택 번호가 같으면 1점+2번~16번 문항 선택 번호가 같으면 1점….195~209번 문항 선택 번호가 같으면 1점
            foreach ($take_arr as $take_key => $take_val) {
                $arr_val = array();
                for ($i = $take_key; $i < $take_key+15; $i++) {
                    if ($take_arr[$i]) {
                        $arr_val[] = $take_arr[$i];
                    }
                }
                if (count(array_count_values($arr_val)) == 1 && count($arr_val)==15) {
                    $continuity++;
                }
            }

            //if(($row['pt_idx'] == 1 || $row['pt_idx'] == 2) && ($type=='basic' || $row['pdt_idx']==5)){		//4restb, 4resta

            if (($row['pt_idx'] == 1 || $row['pt_idx'] == 2)) {		//4restb, 4resta
                $dev_arr['무작위 응답'] = reset(explode(',', $dev_arr['부정 응답'])) + (12-reset(explode(',', $dev_arr['긍정 응답'])));
                $dev_arr['연속 응답'] = $continuity;
                $asking_count = $DB->count_query("select * from test_asking_t where tt_idx=".$row['tt_idx']." and tat_type in(1,2)");
                $take_count = $DB->count_query("select * from test_take_t where ttt_idx=".$row['idx']);
                $dev_arr['무응답'] = $asking_count - $take_count;

            }else if($row['pt_idx'] == 3){  //lmsi
                $dev_arr['무작위 응답'] = reset(explode(',', $dev_arr['부정 응답'])) + (16-reset(explode(',', $dev_arr['긍정 응답'])));
                $dev_arr['연속 응답'] = $continuity;
                $asking_count = $DB->count_query("select * from test_asking_t where tt_idx=".$row['tt_idx']." and tat_type in(1,2)");
                $take_count = $DB->count_query("select * from test_take_t where ttt_idx=".$row['idx']);
                $dev_arr['무응답'] = $asking_count - $take_count;
            }

            //230911 예외사항추가 이가원이사 요청
            if($row['pt_idx']==2 && ($row['pdt_idx']==20 || $row['pdt_idx']==21)){
                $dev_arr['안정애착'] = 21 - $dev_arr['불안애착'] - $dev_arr['회피애착'];
            }

            // 학습 동기 총점 구하기 예외 처리 내재적동기 + 외재적동기 + 32 - 동기결여
            foreach ($dev_arr as $key => $val) {
                if ($key == '종합 점수' && $row['pt_idx']==3) {
                    continue;
                }	//lmsi
                $val_arr = explode(',', $val);
                $val_cnt = $val_arr[1];
                if ($val_arr[1] < 1) {
                    $val_cnt = 1;
                }

                //echo $val.":".$key.":".$val_arr[0]."<br>";
                $set['tct_name'] = $key;	//카테고리 분류
                $set['trt_score'] = $val_arr[0];	//합산 혹은 계산
                $set['trt_count'] = $val_cnt;	//count
                $set['trt_type'] = $type;	//보고서 분류
                $set['trt_wdate'] = "now()";

                $query = $DB->fetch_assoc("select * from test_report_t where to_idx=".$set['to_idx']." and tct_name='".$set['tct_name']."' and trt_type='".$type."'");
                if ($query['idx'] > 0) {
                    //update
                    $DB->update_query("test_report_t", $set, "idx=".$query['idx']);
                } else {
                    //insert
                    $DB->insert_query("test_report_t", $set);
                }
            }

            print_r($dev_arr);
            if($dev_arr['학습 동기']){
                $query_trt_score1 = $DB->fetch_assoc("select trt_score from test_report_t where to_idx=" . $set['to_idx'] . " and tct_name='내재적 동기' and trt_type='" . $type . "'");
                $query_trt_score2 = $DB->fetch_assoc("select trt_score from test_report_t where to_idx=" . $set['to_idx'] . " and tct_name='외재적 동기' and trt_type='" . $type . "'");
                $query_trt_score3 = $DB->fetch_assoc("select trt_score from test_report_t where to_idx=" . $set['to_idx'] . " and tct_name='동기 결여' and trt_type='" . $type . "'");

                $trt_score_total = 32 + $query_trt_score1['trt_score'] + $query_trt_score2['trt_score'] - $query_trt_score3['trt_score'];


                unset($arr_query);
                $arr_query = array(
                    "trt_score" => $trt_score_total,
                );

                $where_query = "to_idx = '" . $set['to_idx'] . "' and tct_name = '학습 동기'";
                $DB->update_query('test_report_t', $arr_query, $where_query);
            }
        }
    }
}

//리포터
function test_report_lmsi($to_idx, $type='basic')
{
    global $DB;
    $report_name = '';
    $temp_arr = array();

    $table_name = "test_category_t";
    $field_name = "tct";
    if ($type=='guardian') {
        $table_name = "test_guardian_t";	//보호자
        $field_name = "tgt";
    }
    if ($type=='teacher') {
        $table_name = "test_teacher_t";	//상담사
        $field_name = "ttt";
    }
    if ($type=='self') {
        $table_name = "test_self_t";	//SELF
        $field_name = "tst";
    }
    if ($type=='professor') {
        $table_name = "test_professor_t";	//교수
        $field_name = "tpt";
    }

    $list = $DB->select_query("select * from test_report_t where to_idx=".$to_idx." and trt_type='".$type."'");
    if ($list) {
        foreach ($list as $val) {
            //$take_to = $DB->fetch_assoc("select GROUP_CONCAT(trt_score) as score from test_report_t where tt_idx=".$val['tt_idx']." and tct_name='".$val['tct_name']."' and trt_type='".$type."' group by tct_name");	//전체 검사의 분류명 and 보고서의 합산을 배열로
            //$z_score = z_dev($val['trt_score'], explode(',', $take_to['score']));	//나의 분류명의 합, 전체 검사 분류명의 합을 배열

            //카테고리 select
            $query = "select * from ".$table_name." where pt_idx = ".$val['pt_idx']." and pdt_idx = ".$val['pdt_idx']." and ".$field_name."_name='".$val['tct_name']."'";
            $row = $DB->fetch_assoc($query);

            /*
            if ($val['tct_name'] != '무응답') {
                //$z_score = (($val['trt_score'] / $val['trt_count']) - $row[$field_name.'_average']) / $row[$field_name.'_standard_deviation'];		//(자신의 원점수-평균)/표준편차
                $z_score = ($val['trt_score'] - $row[$field_name.'_average']) / $row[$field_name.'_standard_deviation'];		//(자신의 원점수-평균)/표준편차
            }

            $t_score = 50 + ($z_score * 10);
            $score = round($t_score);
            */

            if ($val['tct_name'] != '무응답') {
                //$z_score = (($val['trt_score'] / $val['trt_count']) - $row[$field_name.'_average']) / $row[$field_name.'_standard_deviation'];		//(자신의 원점수-평균)/표준편차


                //평균, 표준편차 값이 0일경우는 T점수 계산 안함
                if($row[$field_name.'_average'] == 0 && $row[$field_name.'_standard_deviation'] == 0){
                    $z_score = 0;
                    $t_score = 0;
                    $score = $val['trt_score'];
                }else {
                    $z_score = ($val['trt_score'] - $row[$field_name . '_average']) / $row[$field_name . '_standard_deviation'];        //(자신의 원점수-평균)/표준편차
                    $t_score = 50 + ($z_score * 10);
                    $score = round($t_score);
                }
            }else{
                $z_score = 0;
                $t_score = 0;
                $score = $val['trt_score'];
            }


            if ($val['pt_idx']=='22') {
                $report_name = 'ccci';
                $z_score = '';
                if ($val['tct_name']=='아이디어 (Idea)') {
                    $z_score = ($val['trt_score'] - 64.833) / 10.09;
                }
                if ($val['tct_name']=='사람 (People)') {
                    $z_score = ($val['trt_score'] - 40.02) / 6.367;
                }
                if ($val['tct_name']=='자료 (Data)') {
                    $z_score = ($val['trt_score'] - 52.277) / 7.385;
                }
                if ($val['tct_name']=='실용 (Practical)') {
                    $z_score = ($val['trt_score'] - 45.03) / 5.687;
                }
                if ($val['tct_name']=='외향 (Extrinsic)') {
                    $z_score = ($val['trt_score'] - 33.831) / 6.839;
                }
                if ($z_score) {
                    $t_score = round(10 * $z_score + 50, 2);
                    //$table_name = "test_category_t";
                    //$field_name = "tct";
                    $irt_translate = ($t_score > 49) ? '높음' : '낮음';
                    $irt_nomal_contents = '';
                } else {
                    $t_score = 0;
                    $irt_translate = '';
                }
                $temp_arr[$val['tct_name']] = $t_score;
            }

            $row_score = ($row[$field_name.'_score_num3'] > 0) ? $row[$field_name.'_score_num3'] : $row[$field_name.'_score_num2'];
            $row_score = (int)$row_score;
            if ($t_score > $row_score) {
                $score = $row_score;
            }		//최대수

            if ($row[$field_name.'_score_num1'] >= $score) {
                $irt_translate = $row[$field_name.'_score_var1'];
                $irt_nomal_contents = $row[$field_name.'_score_text1'];
            } elseif ($row[$field_name.'_score_num1'] < $score && $row[$field_name.'_score_num2'] >= $score) {
                $irt_translate = $row[$field_name.'_score_var2'];
                $irt_nomal_contents = $row[$field_name.'_score_text2'];
            } else {
                $irt_translate = $row[$field_name.'_score_var3'];
                $irt_nomal_contents = $row[$field_name.'_score_text3'];
            }

            if ($row[$field_name.'_score_numex'] > 0) {		//예외 처리
                $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and tat_num=".$row[$field_name.'_score_askingex']);
                if ($ttt_row['ttt_select'] >= $row[$field_name.'_score_asking_numex'] && $row[$field_name.'_score_numex'] <= $score) {
                    $irt_translate = $row[$field_name.'_score_varex'];
                    $irt_nomal_contents = $row[$field_name.'_score_textex'];
                }
            }


            //LMSI 개인정보 응답 예외처리
            if ($val['pt_idx']=='3') {
                if ($val['tct_name']=='성적') {
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }

                if ($val['tct_name']=='학습 시간') {
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }

                if ($val['tct_name']=='수면 시간') {
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }

                if ($val['tct_name']=='누적 학점') {
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }

                if ($val['tct_name']=='직전 학기 학점') {
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }

                if ($val['tct_name']=='전공 만족도') {
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }

                if ($val['tct_name']=='성적 만족도') {
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }
                if ($val['tct_name']=='만점 학점') {
                    echo "select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'";
                    $ttt_row = $DB->fetch_assoc("select * from test_take_t where ttt_idx = ".$val['to_idx']." and ttt_class='".$val['tct_name']."'");
                    $trt_row = $DB->fetch_assoc("select * from test_response_t where tt_idx = '".$ttt_row['tt_idx']."' and tat_num='".$ttt_row['tat_num']."' and trt_score='".$ttt_row['ttt_select']."'");
                    $irt_translate = $trt_row['trt_contents'];
                }
            }

            $set = array();
            $set['tct_type'] = $row[$field_name.'_type'];						//대분류
            $set['tct_type_order'] = $row[$field_name.'_type_order'];
            $set['tct_type2'] = $row[$field_name.'_type2'];						//중분류
            $set['tct_type2_order'] = $row[$field_name.'_type2_order'];
            $set['tct_type3'] = $row[$field_name.'_type3'];						//소분류
            $set['tct_type3_order'] = $row[$field_name.'_type3_order'];
            $set['tct_order'] = $row[$field_name.'_order'];


            //강점, 약점, 보완점 LMSI 등록
            if($row[$field_name.'_type'] == $row[$field_name.'_name']) {
                $trt_strength = "";
                $trt_weakness = "";
                $trt_complement = "";

                if($row[$field_name.'_type']=="학습 동기" || $row[$field_name.'_type']=="학습 기술") {
                    $sql_summary1 = "SELECT
                                    tct_name,
                                    CASE
                                        WHEN tct_name = '무동기' THEN 100 - trt_tscore
                                        ELSE trt_tscore
                                    END AS trt_tscore
                                FROM
                                    test_report_t
                                WHERE
                                    to_idx = '".$val['to_idx']."'
                                    AND tct_type = '".$row[$field_name.'_type']."'
                                    AND tct_name != '".$row[$field_name.'_type']."'
                                group by tct_name
                                order by trt_tscore desc limit 0,2";

                    $list_summary1 = $DB->select_query($sql_summary1);

                    if ($list_summary1) {
                        foreach ($list_summary1 as $val_summary1) {
                            $trt_strength = $trt_strength."|".$val_summary1['tct_name'];
                        }
                    }

                    $sql_summary2 = "SELECT
                                    tct_name,
                                    CASE
                                        WHEN tct_name = '무동기' THEN 100 - trt_tscore
                                        ELSE trt_tscore
                                    END AS trt_tscore
                                FROM
                                    test_report_t
                                WHERE
                                    to_idx = '".$val['to_idx']."'
                                    AND tct_type = '".$row[$field_name.'_type']."'
                                    AND tct_name != '".$row[$field_name.'_type']."'
                                group by tct_name
                                order by trt_tscore asc limit 0,2";

                    $list_summary2 = $DB->select_query($sql_summary2);

                    if ($list_summary2) {
                        foreach ($list_summary2 as $val_summary2) {
                            $trt_weakness = $trt_weakness."|".$val_summary2['tct_name'];

                            $calc2 = $DB->fetch_assoc("select tct_text1 from ".$table_name." where tct_name='".$val_summary2['tct_name']."' and pdt_idx=".$row['pdt_idx']);
                            $trt_complement = $trt_complement."|".$calc2['tct_text1'];
                        }
                    }

                    $trt_strength = ltrim($trt_strength, "|");
                    $trt_weakness = ltrim($trt_weakness, "|");
                    $trt_complement = ltrim($trt_complement, "|");
                }else{

                    $sql_summary1 = "SELECT tct_name,trt_tscore                                 
                                    FROM
                                        test_report_t
                                    WHERE
                                        to_idx = '".$val['to_idx']."'
                                        AND tct_type = '".$row[$field_name.'_type']."'
                                        AND tct_name != '".$row[$field_name.'_type']."'
                                    group by tct_name
                                    order by trt_tscore desc limit 0,1";
                    $tst_row1 = $DB->fetch_assoc($sql_summary1);
                    $trt_strength = $tst_row1['tct_name'];

                    $sql_summary2 = "SELECT tct_name,trt_tscore                                 
                                    FROM
                                        test_report_t
                                    WHERE
                                        to_idx = '".$val['to_idx']."'
                                        AND tct_type = '".$row[$field_name.'_type']."'
                                        AND tct_name != '".$row[$field_name.'_type']."'
                                    group by tct_name
                                    order by trt_tscore asc limit 0,1";
                    $tst_row2 = $DB->fetch_assoc($sql_summary2);
                    $trt_weakness = $tst_row2['tct_name'];


                    $tst_row3 = $DB->fetch_assoc("select tct_text1 from ".$table_name." where tct_name='".$tst_row2['tct_name']."' and pdt_idx=".$row['pdt_idx']);
                    $trt_complement = $tst_row3['tct_text1'];
                }



                $set['trt_strength'] = $trt_strength;
                $set['trt_weakness'] = $trt_weakness;
                $set['trt_complement'] = $trt_complement;
            }


            $set['trt_zscore'] = $z_score;
            $set['trt_tscore'] = ($t_score > 100) ? 100 : $t_score;
            $set['trt_translate'] = $irt_translate;						//해석
            $set['trt_nomal_contents'] = $irt_nomal_contents;			//결과
            $set['trt_wdate'] = "now()";

            echo "<pre>";
            print_R($set);
            echo "</pre>";

            $DB->update_query("test_report_t", $set, "idx=".$val['idx']." and trt_type='".$type."'");
        }
    }
    if (count($temp_arr) > 0) {
        $temp_name = array();
        if ($temp_arr['실용 (Practical)'] > $temp_arr['외향 (Extrinsic)'] && $temp_arr['실용 (Practical)'] >= 50) {
            $temp_name['메이커'] = $temp_arr['아이디어 (Idea)'];
        } elseif ($temp_arr['실용 (Practical)'] < $temp_arr['외향 (Extrinsic)'] && $temp_arr['외향 (Extrinsic)'] >= 50) {
            $temp_name['파이어니어'] = $temp_arr['아이디어 (Idea)'];
        } elseif ($temp_arr['실용 (Practical)'] < 50 && $temp_arr['외향 (Extrinsic)'] < 50) {
            $temp_name['스케처'] = $temp_arr['아이디어 (Idea)'];
        }

        if ($temp_arr['실용 (Practical)'] > $temp_arr['외향 (Extrinsic)'] && $temp_arr['실용 (Practical)'] >= 50) {
            $temp_name['디렉터'] = $temp_arr['사람 (People)'];
        } elseif ($temp_arr['실용 (Practical)'] < $temp_arr['외향 (Extrinsic)'] && $temp_arr['외향 (Extrinsic)'] >= 50) {
            $temp_name['커뮤니케이터'] = $temp_arr['사람 (People)'];
        } elseif ($temp_arr['실용 (Practical)'] < 50 && $temp_arr['외향 (Extrinsic)'] < 50) {
            $temp_name['서포터'] = $temp_arr['사람 (People)'];
        }

        if ($temp_arr['실용 (Practical)'] > $temp_arr['외향 (Extrinsic)'] && $temp_arr['실용 (Practical)'] >= 50) {
            $temp_name['씽커'] = $temp_arr['자료 (Data)'];
        } elseif ($temp_arr['실용 (Practical)'] < $temp_arr['외향 (Extrinsic)'] && $temp_arr['외향 (Extrinsic)'] >= 50) {
            $temp_name['파인더'] = $temp_arr['자료 (Data)'];
        } elseif ($temp_arr['실용 (Practical)'] < 50 && $temp_arr['외향 (Extrinsic)'] < 50) {
            $temp_name['체커'] = $temp_arr['자료 (Data)'];
        }
        arsort($temp_name);
        foreach ($temp_name as $key=>$val) {
            $set = array();
            $set['trt_tscore'] = ($val > 100) ? 100 : $val;
            $DB->update_query("test_report_t", $set, "to_idx=".$to_idx." and trt_type='".$type."' and tct_name='".$key."'");
        }
    }
}
?>