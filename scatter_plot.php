<?php
$csv_file = 'PHP Quiz Question #2 - out.csv'; //csv file to read

define('AXIS_MARGIN', 50); //margin for axis as graph won't be all of the svg image, there needs to be space for the labels
define('SVG_WIDTH', 750); //svg width
define('SVG_HEIGHT', 750); //svg height


/*
typical plot will be
['x'=>123, 'y'=>456]
 */
$plots = [];
//get bounds
$min_x = $min_y = PHP_INT_MAX;
$max_x = $max_y = PHP_INT_MIN;

if (($handle = fopen($csv_file, 'r')) !== false){ //open file
    while(($data = fgetcsv($handle, 10, ',')) !== false){ //read each csv line
        list($x, $y) = $data; //assign x and y values
        if (is_numeric($x) && is_numeric($y)){ //check if x and y are numeric
            $plots[] = ['x'=>(int)$x, 'y'=>(int)$y]; //add to plots
            //find min and max, as we want the bounds of the plot points
            $min_x = min($min_x, $x);
            $min_y = min($min_y, $y);
            $max_x = max($max_x, $x);
            $max_y = max($max_y, $y);
        }
    }
    fclose($handle); //close file
}
//set graph bounds, we want to closest 10s
$graph_min_x = floor($min_x / 10) * 10;
$graph_min_y = floor($min_y / 10) * 10;
$graph_max_x = ceil($max_x / 10) * 10;
$graph_max_y = ceil($max_y / 10) * 10;
//Data about the graph, to pass into the translate functions and because I don't want to re-calculate all the time
$graph_data = [
    'graph_min_x' => $graph_min_x, //min x value depicted on the graph
    'graph_min_y' => $graph_min_y, //min y value depicted on the graph
    'graph_max_x' => $graph_max_x, //max x value depicted on the graph
    'graph_max_y' => $graph_max_y, //max y value depicted on the graph
    'graph_width' => $graph_max_x - $graph_min_x, //width depicted on the graph
    'graph_height' => $graph_max_y - $graph_min_y, //height depicted on the graph
    'axis_min_x' => AXIS_MARGIN, //x coordinate of the left side of graph
    'axis_min_y' => SVG_HEIGHT - AXIS_MARGIN, //y coordinate of the bottom of the graph
    'axis_max_x' => SVG_WIDTH - AXIS_MARGIN, //x coordinate of the right side of the graph
    'axis_max_y' => AXIS_MARGIN, //y coordinate of the top of the graph
    'axis_width' => SVG_WIDTH - AXIS_MARGIN * 2, //actual width of the graph
    'axis_height' => SVG_HEIGHT - AXIS_MARGIN *2, //actual height of the graph
];

/**
 * Translate the x value of the plot point to the x coordinate in the svg image
 * @param $x int: x value of the plot point
 * @param $graph_data array: data about the graph
 * Possible test cases:
 * x as graph_min_x, return value should be axis_min_x
 * x as graph_max_x, return value should be axis_max_x
 * @return float: x coordinate in the svg image
 */
function translate_x($x, $graph_data){
    $ratio = $graph_data['axis_width'] / $graph_data['graph_width']; //the ratio of graph width to svg width
    $graph_x = $ratio * ($x - $graph_data['graph_min_x']) + $graph_data['axis_min_x']; //ratio*(x - graph min x) + axis min x
    return $graph_x;
}
/**
 * Translate the y value of the plot point to the y coordinate in the svg image.
 * Remember that y coordinates are inverted in the svg, because origin is top left, but we're depicting bottom left as origin
 * @param $y int: y value of the plot point
 * @param $graph_data array: data about the graph
 * Possible test cases:
 * y as graph_min_y, return value should be axis_min_y
 * y as graph_max_y, return value should be axis_max_y
 * @return float: y coordinate in the svg image
 */
function translate_y($y, $graph_data){
    $ratio = $graph_data['axis_height'] / $graph_data['graph_height']; //the ratio of graph height to svg height
    $graph_y = $graph_data['axis_min_y'] - $ratio * ($y - $graph_data['graph_min_y']); //axis min y - ratio*(y - graph min y)
    return $graph_y;
}

/**
 * Output will be SVG since I feel it's the best way to show the plots
 * We will draw the x and y axis first
 * Then the plot the points, by looping through $plots array and drawing a circle for each point
 * Each point will be translated to coorindates in the svg image
 */
?>
<svg version="1.1" width="<?=SVG_WIDTH?>" height="<?=SVG_HEIGHT?>" xmlns="http://www.w3.org/2000/svg">
    <!-- draw y axis -->
    <line id="y-axis" x1="<?= $graph_data['axis_min_x']?>" y1="<?= $graph_data['axis_max_y']?>" x2="<?= $graph_data['axis_min_x']?>" y2="<?= $graph_data['axis_min_y']?>" stroke="black" stroke-width="1" />
    <text id="y-axis-max" x="<?= $graph_data['axis_min_x']?>" y="<?= $graph_data['axis_max_y']?>" dominant-baseline="middle" dx="-35"><?=$graph_max_y?></text>
    <text id="y-axis-min" x="<?= $graph_data['axis_min_x']?>" y="<?= $graph_data['axis_min_y']?>" dominant-baseline="middle" dx="-35"><?=$graph_min_y?></text>

    <!-- draw x axis -->
    <line id="x-axis" x1="<?= $graph_data['axis_min_x']?>" y1="<?= $graph_data['axis_min_y']?>" x2="<?= $graph_data['axis_max_x']?>" y2="<?= $graph_data['axis_min_y']?>" stroke="black" stroke-width="1" />
    <text id="x-axis-min" x="<?= $graph_data['axis_min_x']?>" y="<?= $graph_data['axis_min_y']?>" dominant-baseline="top" dy="20" dx="-17"><?=$graph_min_x?></text>
    <text id="x-axis-max" x="<?= $graph_data['axis_max_x']?>" y="<?= $graph_data['axis_min_y']?>" dominant-baseline="top" dy="20" dx="-17"><?=$graph_max_x?></text>

    <!-- draw plots -->
     <?php foreach($plots as $num=>$plot){?>
        <circle cx="<?=translate_x($plot['x'], $graph_data)?>" cy="<?=translate_y($plot['y'], $graph_data)?>" r="3" stroke="black" fill="grey" />
     <?php }?>
</svg>

