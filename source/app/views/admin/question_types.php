<?php
echo '<p>'.$desc.'</p>';
switch ($typeID) {
    // fact or fiction \\
    case 1:
        ?>
        <p>Statement</p>
        <ul>
        <?php
        $answers = $this->Admin->getAnswers($id);
        if (count($answers) > 0 && $answers[0]['type_id'] == 1) {
            echo '<li id="'.$answers[0]['id'].'"><span style="float: left; display: block;"><label for="fact_0">Fact?</label> <input type="radio" name="correct[]" id="fact_0" value="1" '.($answers[0]['correct'] == '1' ? 'checked="checked"' : '').' /></span><span style="float: left; display: block; padding-top: 4px;"> or </span><span style="float: left; display: block;"><label for="fact_1">Fiction?</label> <input type="radio" name="correct[]" id="fact_1" value="0" '.($answers[0]['correct'] == '0' ? 'checked="checked"' : '').' /></span></li>';
        } else {
            ?>
            <li class="answerHolder"><span style="float: left; display: block;"><label for="fact_0">Fact?</label> <input type="radio" name="correct[]" id="fact_0" value="1" /></span><span style="float: left; display: block; padding-top: 4px;"> or </span><span style="float: left; display: block;"><label for="fact_1">Fiction?</label> <input type="radio" name="correct[]" id="fact_1" value="0" /></span></li>
            <?php
        }
        ?>
        </ul>
        <?php
        break;
    // drag and drop \\
    case 2:
        // need to select a sub drag and drop item \\
        ?>
        <label for="sub_type">Question sub type</label>
        <select name="sub_type" id="sub_type">
             <?php
             $subTypes = $this->Admin->getQuestionSubTypes($typeID);
             $subDesc = '';
             foreach ($subTypes as $subType) {
                 echo '<option value="'.$subType['id'].'" '.(($subType['id'] == $subTypeID) ? 'selected="selected"' : '').'>'.$subType['sub_type'].'</option>';
                 $subDesc .= '<p class="desc_'.$subType['id'].'" style="display:none;">'.$subType['description'].'</p>';
             }
             ?>
        </select>
        <div class="clearme"></div>
        <?php
        echo $subDesc;
        ?>
        <div id="hidden_1" class="sub_types">
            <h4>Answers - in correct order</h4>
            <ul>
                <?php
                if ($id != 0) {
                    $answer = $this->Admin->getAnswers($id);
                    if (count($answer) > 0 && $subTypeID == 1) {
                        $answers1 = json_decode($answer[0]['answer'], true);
                        if (!is_null($answers1)) {
                            foreach ($answers1 as $key=>$answer) {
                                echo '<li id="'.$key.'"><input type="text" name="ans_code[]" value="'.$answer['code'].'" size="10" /><input type="text" name="answer[]" value="'.$answer['answer'].'" /><input type="button" name="remove" value="X" /></li>';
                            }
                        }
                    }
                }
                ?>
                <li class="answerHolder"><input type="text" name="ans_code[]" placeholder="Code" size="10" /><input type="text" name="answer[]" placeholder="Answer" /><input type="button" name="remove" value="X" /></li>
                <li><input type="button" name="add" value="Add answer" /></li>
            </ul>
        </div>
        <div id="hidden_2" class="sub_types">
            <h4>Answers - statement matching</h4>
            <ul>
                <?php
                if ($id != 0) {
                    $answer = $this->Admin->getAnswers($id);
                    if (count($answer) > 0 && $subTypeID == 2) {
                        $answers2 = json_decode($answer[0]['answer'], true);
                        if (!is_null($answers2)) {
                            foreach ($answers2 as $key=>$answer) {
                                echo '<li id="'.$key.'"><input type="text" name="ans_code[]" value="'.$answer['code'].'" size="10" /><input type="text" name="phrase[]" value="'.$answer['phrase'].'" /><input type="text" name="statement[]" value="'.$answer['statement'].'" /><input type="button" name="remove" value="X" /></li>';
                            }
                        }
                    }
                }
                ?>
                <li class="answerHolder"><input type="text" name="ans_code[]" placeholder="Code" size="10" /><input type="text" name="phrase[]" placeholder="Phrase" /><input type="text" name="statement[]" placeholder="Statement" /><input type="button" name="remove" value="X" /></li>
                <li><input type="button" name="add" value="Add answer" /></li>
            </ul>
        </div>
        <div id="hidden_3" class="sub_types sorting">
            <?php
            if ($id != 0) {
                $answers = $this->Admin->getAnswers($id);
                if (count($answers) > 0 && $subTypeID == 3) {
                    $answer = json_decode($answers[0]['answer'], true);
                }
            }
            ?>
            <h4>Column titles</h4>
            <ul id="columns">
                <?php
                $options = array();
                if (isset($answer) && isset($answer['columns'])) {
                    foreach ($answer['columns'] as $i=>$col) {
                        echo '<li><input type="text" name="column[]" value="'.$col.'" /><input type="button" name="remove" value="X" /></li>';
                        $options[] = $col;
                    }
                }
                ?>
                <li class="columnHolder"><input type="text" name="column[]" placeholder="Column" /><input type="button" name="remove" value="X" /></li>
                <li><input type="button" name="add" value="Add column" /></li>
            </ul>
            <h4>Pre-filled items</h4>
            <ul>
                <?php
                if (isset($answer) && isset($answer['pre-filled'])) {
                    foreach ($answer['pre-filled'] as $pre) {
                        $preOpts = '';
                        foreach ($options as $key=>$val) {
                            $preOpts .= '<option value="'.($key).'" '.(($key == $pre['column']) ? 'selected="selected"' : '').'>'.$val.'</option>';
                        }
                        echo '<li><input type="text" name="pre-filled[]" value="'.$pre['text'].'" /><select name="assigned[]">'.$preOpts.'</select><input type="button" name="remove" value="X" /></li>';
                    }
                }
                ?>
                <li class="filledHolder">
                    <input type="text" name="pre-filled[]" placeholder="Pre-filled" />
                    <select name="assigned[]">
                        <?php
                        foreach ($options as $i=>$opt) {
                            echo '<option value="'.$i.'">'.$opt.'</option>';
                        }
                        ?>
                    </select>
                    <input type="button" name="remove" value="X" />
                </li>
                <li><input type="button" name="add" value="Add pre-filled" /></li>
            </ul>
            <h4>Draggable items</h4>
            <ul>
                <?php
                if (isset($answer) && isset($answer['answer'])) {
                    foreach ($answer['answer'] as $ans) {
                        $ansOpts = '';
                        foreach ($options as $key=>$val) {
                            $ansOpts .= '<option value="'.($key).'" '.(($key == $ans['column']) ? 'selected="selected"' : '').'>'.$val.'</option>';
                        }
                        echo '<li><input type="text" name="answer[]" value="'.$ans['text'].'" /><select name="cor[]">'.$ansOpts.'</select><input type="button" name="remove" value="X" /></li>';
                    }
                }
                ?>
                <li class="answerHolder">
                    <input type="text" name="answer[]" placeholder="Draggable" />
                    <select name="cor[]">
                        <?php
                        foreach ($options as $i=>$opt) {
                            echo '<option value="'.$i.'">'.$opt.'</option>';
                        }
                        ?>
                    </select>
                    <input type="button" name="remove" value="X" />
                </li>
                <li><input type="button" name="add" value="Add draggable item" /></li>
            </ul>
        </div>
        <div id="hidden_4" class="sub_types pic_matching">
            <?php
            $answer = null;
            if ($id != 0) {
                $answers = $this->Admin->getAnswers($id);
                if (count($answers) > 0 && $subTypeID == 4) {
                    $answer = json_decode($answers[0]['answer'], true);
                }
            }
            ?>
            <h4>Image</h4>
            <ul>
                <li><?php echo (!is_null($answer) && $subTypeID == 4 && array_key_exists('image', $answer)) ? '<input type="hidden" name="answer_id" value="'.$answers[0]['id'].'" /><strong>Current file </strong>'.str_replace(uploadDir, '', $answer['image']): ''; ?><input type="file" name="image" /></li>
            </ul>
            <h4>Descriptions</h4>
            <ul>
                <?php
                if (!is_null($answer) && isset($answer['answers'])) {
                    foreach ($answer['answers'] as $ans) {
                        echo '<li><span style="float: left; display: block;">Correct -  '.($ans['correct'] == '1' ? '<input type="hidden" name="masked[]" value="0" />' : '<input type="hidden" name="correct[]" value="" />').'<input type="checkbox" name="correct[]" value="1" '.($ans['correct'] == '1' ? 'checked="checked"' : '').' /></span><input type="text" name="desc[]" value="'.$ans['desc'].'" /><input type="button" name="remove" value="X" /></li>';
                    }
                }
                ?>
                <li class="answerHolder"><span style="float: left; display: block;">Correct - <input type="hidden" name="correct[]" value="0" /><input type="checkbox" name="correct[]" value="1" /></span><input type="text" name="desc[]" placeholder="Description" /><input type="button" name="remove" value="X" /></li>
                <li><input type="button" name="add" value="Add description" /></li>
            </ul>
        </div>
        <script type="text/javascript">
        $(function() {
            $("select[name='sub_type']").each(selSub);
            $("select[name='sub_type']").change(selSub);
            function selSub() {
                $("div.sub_types").hide();
                $("div#hidden_"+$(this).val()).show();
                $("div#hidden_"+$(this).val()).find('p.desc_'+$(this).val()).empty().remove();
                $("div#hidden_"+$(this).val()).prepend($("p.desc_"+$(this).val()).clone().show());
            }
            $("div.sub_types[class^='sorting']").each(function() {
                var cloneAns = $("li.answerHolder", this).clone();
                $("input[name='add']", this).click(function() {
                    $(this).parent().before(cloneAns.clone());
                });
            });
            $("div.sub_types.sorting").each(function() {
                var cloneCol = $("li.columnHolder", this).clone(),
                    cloneFil = $("li.filledHolder", this).clone(),
                    cloneDrag = $("li.answerHolder", this).clone();
                var $that = this;
                $("input[name='add']", this).click(function() {
                    if ($(this).parent().parent().is('#columns')) {
                        var clonedCol = cloneCol.clone();
                        $(this).parent().before(clonedCol);
                        var ind = clonedCol.index();
                        $("select[name='correct[]'], select[name='assigned[]']", $that).append('<option value="'+ind+'"></option>');
                        $("select[name='assigned[]']", cloneFil).append('<option value="'+ind+'"></option>');
                        $("select[name='correct[]']", cloneDrag).append('<option value="'+ind+'"></option>');
                    } else if ($(this).parent().prev().hasClass('filledHolder')) {
                        $(this).parent().before(cloneFil.clone());
                    } else {
                        $(this).parent().before(cloneDrag.clone());
                    }
                });
                $("ul#columns li", this).each(function(x) {
                    if (x+1 < $(this).parent().find('li').length && $(this).hasClass('columnHolder')) {
                        $("select[name='correct[]'], select[name='assigned[]']", $that).append('<option value="'+x+'"></option>');
                        $("select[name='assigned[]']", cloneFil).append('<option value="'+x+'"></option>');
                        $("select[name='correct[]']", cloneDrag).append('<option value="'+x+'"></option>');
                    }
                });
                $(this).on('keyup', "li.columnHolder input[name='column[]']", function() {
                    var ind = $(this).parent().index();
                    $("select[name='correct[]'] option[value='"+ind+"'], select[name='assigned[]'] option[value='"+ind+"']", $that).text($(this).val());
                    $("select[name='assigned[]'] option[value='"+ind+"']", cloneFil).text($(this).val());
                    $("select[name='correct[]'] option[value='"+ind+"']", cloneDrag).text($(this).val());
                });
            });
            $("div.sub_types.pic_matching").each(function() {
                var newAnswer = $("li.answerHolder", this).clone();
                $("input[name='add']", this).click(function() {
                    $(this).parent().before(newAnswer.clone());
                });
                $("ul").on('click', "input[name='remove']", function() {
                    $(this).parent().remove();
                });
                $("ul").on('change', "input[name='correct[]']", function() {
                    if ($(this).is(':checked')) {
                        $(this).prev('input').prop('name', 'masked[]');
                    } else {
                        $(this).prev('input').prop('name', 'correct[]');
                    }
                });
            });
            
            $("ul").on('click', "input[name='remove']", function() {
                if ($(this).parent().parent().is('#columns')) {
                    var ind = $(this).parent().index();
                    $("select[name='correct[]'] option[value='"+ind+"'], select[name='assigned[]'] option[value='"+ind+"']").empty().remove();
                    $("select[name='correct[]'] option, select[name='assigned[]'] option").each(function() {
                        if ($(this).index() >= ind) {
                            $(this).val($(this).index());
                        }
                    });                    
                }
                $(this).parent().empty().remove();
            });
        });
        </script>
        <?php
        break;
    // fill in the blank \\
    case 3:
        ?>
        <p>Accepted words</p>
        <p>Enter all accepted words below - if multiple spellings of words or multiple correct words exist, these can be added as additional entries</p>
        <ul>
            <?php
            $answers = $this->Admin->getAnswers($id);
            if (count($answers) > 0 && $answers[0]['type_id'] == 3) {
                foreach ($answers as $answer) {
                    echo '<li id="'.$answer['id'].'"><input type="text" name="ans_code[]" value="'.$answer['code'].'" size="10" /><input type="text" name="answer[]" value="'.$answer['answer'].'" /><input type="button" name="remove" value="X" /></li>';
                }
            }
            ?>
            <li class="answerHolder"><input type="text" name="ans_code[]" placeholder="Code" size="10" /><input type="text" name="answer[]" placeholder="Answer" /><input type="button" name="remove" value="X" /></li>
            <li><input type="button" name="add" value="Add answer" /></li>
        </ul>
        <script type="text/javascript">
            $(function() {
                var newAnswer = $("li.answerHolder").clone();
                $("input[name='add']").click(function() {
                    $(this).parent().before(newAnswer.clone());
                });
                $("ul").on('click', "input[name='remove']", function() {
                    $(this).parent().remove();
                });
            });
        </script>
    <?php
        break;
    // multiple choice
    case 4:
        ?>
    <p>Answers</p>
    <ul>
        <?php
        $answers = $this->Admin->getAnswers($id);
        if (count($answers) > 0 && $answers[0]['type_id'] == 4) {
            foreach ($answers as $answer) {
                echo '<li id="'.$answer['id'].'"><span style="float: left; display: block;">Correct -  '.($answer['correct'] == '1' ? '<input type="hidden" name="masked[]" value="0" />' : '<input type="hidden" name="correct[]" value="" />').'<input type="checkbox" name="correct[]" value="1" '.($answer['correct'] == '1' ? 'checked="checked"' : '').' /></span><input type="text" name="ans_code[]" value="'.$answer['code'].'" size="10" /><input type="text" name="answer[]" value="'.$answer['answer'].'" /><input type="button" name="remove" value="X" /></li>';
            }
        }
        ?>
        <li class="answerHolder"><span style="float: left; display: block;">Correct - <input type="hidden" name="correct[]" value="0" /><input type="checkbox" name="correct[]" value="1" /></span><input type="text" name="ans_code[]" placeholder="Code" size="10" /><input type="text" name="answer[]" placeholder="Answer" /><input type="button" name="remove" value="X" /></li>
        <li><input type="button" name="add" value="Add answer" /></li>
    </ul>
    <script type="text/javascript">
        $(function() {
            var newAnswer = $("li.answerHolder").clone();
            $("input[name='add']").click(function() {
                $(this).parent().before(newAnswer.clone());
            });
            $("ul").on('click', "input[name='remove']", function() {
                $(this).parent().remove();
            });
            $("ul").on('change', "input[name='correct[]']", function() {
                if ($(this).is(':checked')) {
                    $(this).prev('input').prop('name', 'masked[]');
                } else {
                    $(this).prev('input').prop('name', 'correct[]');
                }
            });
        });
    </script>
<?php
        break;
    // letter shuffle \\
    case 5:
        ?>
    <p>Enter the word / phrase below</p>
    <ul>
        <?php
        $answer = $this->Admin->getAnswers($id);
        if (count($answer) > 0 && $answer[0]['type_id'] == 5) {
            echo '<li id="'.$answer[0]['id'].'"><input type="text" name="ans_code[]" value="'.$answer[0]['code'].'" size="10" /><input type="text" name="answer[]" value="'.$answer[0]['answer'].'" /></li>';
        } else {
        ?>
        <li class="answerHolder"><input type="text" name="ans_code[]" placeholder="Code" size="10" /><input type="text" name="answer[]" placeholder="Answer" /></li>
        <?php
        }
        ?>
    </ul>
    <?php
        break;
    // picture multiple choice \\
    case 6:
        ?>
        <p>Answers</p>
    <ul>
        <?php
        $answers = $this->Admin->getAnswers($id);
        if (count($answers) > 0 && $answers[0]['type_id'] == 6) {
            foreach ($answers as $answer) {
                $ans = json_decode($answer['answer'], true);
                echo '<li id="'.$answer['id'].'"><input type="hidden" name="answer_id[]" value="'.$answer['id'].'" /><span style="float: left; display: block;">Correct -  '.($answer['correct'] == '1' ? '<input type="hidden" name="masked[]" value="0" />' : '<input type="hidden" name="correct[]" value="" />').'<input type="checkbox" name="correct[]" value="1" '.($answer['correct'] == '1' ? 'checked="checked"' : '').' /></span><input type="text" name="ans_code[]" value="'.$answer['code'].'" size="10" /><input type="text" name="answer[]" value="'.$ans['answer'].'" /><strong>Current file:</strong>'.str_replace(uploadDir, '', $ans['file']).'<input type="file" name="image[]" /><input type="button" name="remove" value="X" /></li>';
            }
        }
        ?>
        <li class="answerHolder"><span style="float: left; display: block;">Correct - <input type="hidden" name="correct[]" value="0" /><input type="checkbox" name="correct[]" value="1" /></span><input type="text" name="ans_code[]" placeholder="Code" size="10" /><input type="text" name="answer[]" placeholder="Description" /><input type="file" name="image[]" /><input type="button" name="remove" value="X" /></li>
        <li><input type="button" name="add" value="Add answer" /></li>
    </ul>
    <script type="text/javascript">
        $(function() {
            var newAnswer = $("li.answerHolder").clone();
            $("input[name='add']").click(function() {
                $(this).parent().before(newAnswer.clone());
            });
            $("ul").on('click', "input[name='remove']", function() {
                $(this).parent().remove();
            });
            $("ul").on('change', "input[name='correct[]']", function() {
                if ($(this).is(':checked')) {
                    $(this).prev('input').prop('name', 'masked[]');
                } else {
                    $(this).prev('input').prop('name', 'correct[]');
                }
            });
        });
    </script>
    <?php
        break;
    // scenario based multiple choice \\
    case 7:
        ?>
    <p>Answers</p>
    <ul>
        <?php
        $answers = $this->Admin->getAnswers($id);
        if (count($answers) > 0 && $answers[0]['type_id'] == 6) {
            foreach ($answers as $answer) {
                echo '<li id="'.$answer['id'].'"><span style="float: left; display: block;">Correct -  '.($answer['correct'] == '1' ? '<input type="hidden" name="masked[]" value="0" />' : '<input type="hidden" name="correct[]" value="" />').'<input type="checkbox" name="correct[]" value="1" '.($answer['correct'] == '1' ? 'checked="checked"' : '').' /></span><input type="text" name="ans_code[]" value="'.$answer['code'].'" size="10" /><textarea name="answer[]">'.$answer['answer'].'</textarea><input type="button" name="remove" value="X" /></li>';
            }
        }
        ?>
        <li class="answerHolder"><span style="float: left; display: block;">Correct - <input type="hidden" name="correct[]" value="0" /><input type="checkbox" name="correct[]" value="1" /></span><input type="text" name="ans_code[]" placeholder="Code" size="10" /><textarea name="answer[]" placeholder="Answer"></textarea><input type="button" name="remove" value="X" /></li>
        <li><input type="button" name="add" value="Add answer" /></li>
    </ul>
    <script type="text/javascript">
        $(function() {
            var newAnswer = $("li.answerHolder").clone();
            $("input[name='add']").click(function() {
                $(this).parent().before(newAnswer.clone());
            });
            $("ul").on('click', "input[name='remove']", function() {
                $(this).parent().remove();
            });
            $("ul").on('change', "input[name='correct[]']", function() {
                if ($(this).is(':checked')) {
                    $(this).prev('input').prop('name', 'masked[]');
                } else {
                    $(this).prev('input').prop('name', 'correct[]');
                }
            });
        });
    </script>
    <?php
        break;
}
?>
