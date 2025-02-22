﻿# PHP quiz questions

These questions were run locally on a WAMP server.

## 1. The Telesign API Call

The original code is in the file *telesign_original.php* and the fixed code is in the file *telesign_fixed.php*. It was run with an actual phone number, customer_id and api key despite not been used in the repo.

## 2. The scatter plot

In the file *scatter_plot.php* which uses the file *PHP Quiz Question #2 - out.csv* as it's data source, this was done with SVG output even though it could have also been JavaScript Canvas or the usage of some drawing library for PHP. It felt easier to write it out as SVG code as the results could be examined better in the browser.

I handled the scatter plot like it was a graph, so I included x and y axis lines, with labels of the minimum and maximum values of the plot points, but didn't feel the need to mark any part of the axis lines in between. Each plot point is represented as a grey circle. 

Yes, it's an image of a peeled banana.
