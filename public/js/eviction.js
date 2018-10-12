$(document).ready(function () {

    $('#filing_date').val(new Date());
    $('#landlord').prop('hidden', true);

    var map;
    var marker;
    var bounds;
    var houseNum;
    var streetName;
    var town;
    var county;
    var zipcode;
    var state;

    var center = new google.maps.LatLng(40.149660, -76.306370);
    //Create the areas for magistrates

    var magistrate02101Area = [
        {lng: -76.3059084, lat: 40.0222321}, {lng: -76.3048784, lat: 40.022725}, {lng: -76.3034622, lat: 40.026077}, {lng: -76.3053934, lat: 40.0367073}, {lng: -76.3055007, lat: 40.0371016}, {lng: -76.305608, lat: 40.0374466}, {lng: -76.3056643, lat: 40.0376971}, {lng: -76.305785, lat: 40.0378984}, {lng: -76.3086657, lat: 40.0376273}, {lng: -76.3098352, lat: 40.0375123}, {lng: -76.3121311, lat: 40.037348}, {lng: -76.3152478, lat: 40.0369579}, {lng: -76.3166479, lat: 40.0373563}, {lng: -76.3185469, lat: 40.0378246}, {lng: -76.3236325, lat: 40.038268}, {lng: -76.3243621, lat: 40.0352943}, {lng: -76.3245874, lat: 40.0344113}, {lng: -76.324641, lat: 40.0338239}, {lng: -76.324877, lat: 40.0321235}, {lng: -76.3245337, lat: 40.0297904}, {lng: -76.3231175, lat: 40.0296918}, {lng: -76.3220017, lat: 40.0273916}, {lng: -76.3250057, lat: 40.0247625}, {lng: -76.3234608, lat: 40.0235795}, {lng: -76.3243191, lat: 40.0226593}, {lng: -76.32286, lat: 40.0215419}, {lng: -76.3238041, lat: 40.0207531}, {lng: -76.3214009, lat: 40.0187155}, {lng: -76.3197701, lat: 40.0205559}, {lng: -76.3152211, lat: 40.0177952}, {lng: -76.3059084, lat: 40.0222321}
    ];
    var magistrate02103Area = [
        {lng: -76.438261, lat: 40.045007}, {lng: -76.438325, lat: 40.045078}, {lng: -76.438363, lat: 40.045211}, {lng: -76.4384, lat: 40.045334}, {lng: -76.438446, lat: 40.045478}, {lng: -76.438569, lat: 40.045837}, {lng: -76.438605, lat: 40.045947}, {lng: -76.438641, lat: 40.046055}, {lng: -76.43869, lat: 40.04619}, {lng: -76.438693, lat: 40.046199}, {lng: -76.438695, lat: 40.046204}, {lng: -76.438702, lat: 40.046223}, {lng: -76.438718, lat: 40.046254}, {lng: -76.438772, lat: 40.046359}, {lng: -76.438816, lat: 40.046416}, {lng: -76.438837, lat: 40.046444}, {lng: -76.43884, lat: 40.046447}, {lng: -76.438866, lat: 40.04648}, {lng: -76.438895, lat: 40.04651}, {lng: -76.438971, lat: 40.046585}, {lng: -76.439013, lat: 40.046624}, {lng: -76.439067, lat: 40.046675}, {lng: -76.439245, lat: 40.046831}, {lng: -76.439278, lat: 40.046859}, {lng: -76.439311, lat: 40.046887}, {lng: -76.439409, lat: 40.04697}, {lng: -76.439483, lat: 40.047057}, {lng: -76.439556, lat: 40.047143}, {lng: -76.439577, lat: 40.04714}, {lng: -76.439598, lat: 40.047136}, {lng: -76.440653, lat: 40.046975}, {lng: -76.440727, lat: 40.046964}, {lng: -76.440733, lat: 40.046963}, {lng: -76.440994, lat: 40.046923}, {lng: -76.441431, lat: 40.046865}, {lng: -76.441814, lat: 40.046817}, {lng: -76.441817, lat: 40.046817}, {lng: -76.442992, lat: 40.046668}, {lng: -76.445181, lat: 40.04639}, {lng: -76.446695, lat: 40.046198}, {lng: -76.447239, lat: 40.046129}, {lng: -76.447273, lat: 40.046125}, {lng: -76.447273, lat: 40.046119}, {lng: -76.447274, lat: 40.045817}, {lng: -76.447274, lat: 40.045812}, {lng: -76.447274, lat: 40.045811}, {lng: -76.447265, lat: 40.045753}, {lng: -76.447255, lat: 40.045686}, {lng: -76.447217, lat: 40.045464}, {lng: -76.447214, lat: 40.045449}, {lng: -76.447212, lat: 40.045433}, {lng: -76.447181, lat: 40.045238}, {lng: -76.447159, lat: 40.045107}, {lng: -76.447159, lat: 40.045106}, {lng: -76.447134, lat: 40.044978}, {lng: -76.447098, lat: 40.044792}, {lng: -76.447059, lat: 40.04456}, {lng: -76.44704, lat: 40.044432}, {lng: -76.447038, lat: 40.044418}, {lng: -76.447036, lat: 40.044404}, {lng: -76.447019, lat: 40.04428}, {lng: -76.447007, lat: 40.044187}, {lng: -76.446995, lat: 40.044062}, {lng: -76.446989, lat: 40.043982}, {lng: -76.446984, lat: 40.043863}, {lng: -76.446982, lat: 40.04377}, {lng: -76.446972, lat: 40.043724}, {lng: -76.446967, lat: 40.043702}, {lng: -76.446954, lat: 40.043646}, {lng: -76.446813, lat: 40.043018}, {lng: -76.446778, lat: 40.042867}, {lng: -76.44677, lat: 40.042829}, {lng: -76.446764, lat: 40.042791}, {lng: -76.446751, lat: 40.042706}, {lng: -76.446743, lat: 40.042639}, {lng: -76.446736, lat: 40.042588}, {lng: -76.446714, lat: 40.042456}, {lng: -76.446689, lat: 40.042304}, {lng: -76.446625, lat: 40.041942}, {lng: -76.446619, lat: 40.041909}, {lng: -76.446614, lat: 40.041877}, {lng: -76.446599, lat: 40.041778}, {lng: -76.446567, lat: 40.041582}, {lng: -76.446527, lat: 40.041338}, {lng: -76.446495, lat: 40.041183}, {lng: -76.446468, lat: 40.04105}, {lng: -76.446446, lat: 40.040931}, {lng: -76.446425, lat: 40.040813}, {lng: -76.446314, lat: 40.040301}, {lng: -76.446312, lat: 40.040292}, {lng: -76.446081, lat: 40.039232}, {lng: -76.445547, lat: 40.039283}, {lng: -76.445502, lat: 40.039026}, {lng: -76.445468, lat: 40.038833}, {lng: -76.446057, lat: 40.038777}, {lng: -76.446052, lat: 40.038769}, {lng: -76.445968, lat: 40.038638}, {lng: -76.445439, lat: 40.038683}, {lng: -76.445397, lat: 40.038461}, {lng: -76.44535, lat: 40.038215}, {lng: -76.445502, lat: 40.038204}, {lng: -76.445946, lat: 40.038163}, {lng: -76.445943, lat: 40.038153}, {lng: -76.445781, lat: 40.037624}, {lng: -76.44462, lat: 40.03768}, {lng: -76.444604, lat: 40.037681}, {lng: -76.444469, lat: 40.037687}, {lng: -76.443833, lat: 40.037712}, {lng: -76.443422, lat: 40.037728}, {lng: -76.443212, lat: 40.03774}, {lng: -76.443121, lat: 40.037745}, {lng: -76.442617, lat: 40.037786}, {lng: -76.442377, lat: 40.037813}, {lng: -76.44236, lat: 40.037743}, {lng: -76.442351, lat: 40.037709}, {lng: -76.44234, lat: 40.037675}, {lng: -76.442054, lat: 40.036876}, {lng: -76.442043, lat: 40.036846}, {lng: -76.442008, lat: 40.03675}, {lng: -76.441955, lat: 40.036604}, {lng: -76.441862, lat: 40.03626}, {lng: -76.441781, lat: 40.035962}, {lng: -76.441781, lat: 40.035961}, {lng: -76.441518, lat: 40.035048}, {lng: -76.442073, lat: 40.034804}, {lng: -76.442581, lat: 40.034454}, {lng: -76.442373, lat: 40.03407}, {lng: -76.441197, lat: 40.034404}, {lng: -76.439445, lat: 40.0349}, {lng: -76.439347, lat: 40.034832}, {lng: -76.439347, lat: 40.034831}, {lng: -76.439333, lat: 40.034822}, {lng: -76.439318, lat: 40.034822}, {lng: -76.43918, lat: 40.034825}, {lng: -76.439048, lat: 40.034827}, {lng: -76.438931, lat: 40.03483}, {lng: -76.433209, lat: 40.034944}, {lng: -76.432521, lat: 40.034958}, {lng: -76.432476, lat: 40.034963}, {lng: -76.432148, lat: 40.035001}, {lng: -76.432146, lat: 40.035002}, {lng: -76.431953, lat: 40.035027}, {lng: -76.430788, lat: 40.035183}, {lng: -76.430166, lat: 40.035268}, {lng: -76.429741, lat: 40.035325}, {lng: -76.429722, lat: 40.035327}, {lng: -76.428246, lat: 40.035476}, {lng: -76.427992, lat: 40.035502}, {lng: -76.425408, lat: 40.035763}, {lng: -76.424995, lat: 40.03577}, {lng: -76.424534, lat: 40.035781}, {lng: -76.424061, lat: 40.035793}, {lng: -76.422465, lat: 40.035722}, {lng: -76.422042, lat: 40.035695}, {lng: -76.4214, lat: 40.035665}, {lng: -76.42138, lat: 40.035664}, {lng: -76.418476, lat: 40.035526}, {lng: -76.418477, lat: 40.035533}, {lng: -76.418478, lat: 40.035545}, {lng: -76.418597, lat: 40.036785}, {lng: -76.418597, lat: 40.036787}, {lng: -76.418668, lat: 40.037013}, {lng: -76.418681, lat: 40.037146}, {lng: -76.418693, lat: 40.037344}, {lng: -76.419259, lat: 40.037871}, {lng: -76.419283, lat: 40.037894}, {lng: -76.419746, lat: 40.038328}, {lng: -76.420273, lat: 40.038823}, {lng: -76.420381, lat: 40.038909}, {lng: -76.420467, lat: 40.038991}, {lng: -76.420521, lat: 40.039082}, {lng: -76.420521, lat: 40.039083}, {lng: -76.420522, lat: 40.039085}, {lng: -76.421056, lat: 40.03998}, {lng: -76.421473, lat: 40.040679}, {lng: -76.421677, lat: 40.041022}, {lng: -76.421724, lat: 40.0411}, {lng: -76.421767, lat: 40.041173}, {lng: -76.423411, lat: 40.042884}, {lng: -76.423409, lat: 40.042892}, {lng: -76.423395, lat: 40.042961}, {lng: -76.423246, lat: 40.043689}, {lng: -76.423054, lat: 40.044636}, {lng: -76.423048, lat: 40.044644}, {lng: -76.422781, lat: 40.044998}, {lng: -76.422643, lat: 40.045177}, {lng: -76.42341, lat: 40.045166}, {lng: -76.424624, lat: 40.04515}, {lng: -76.426756, lat: 40.044728}, {lng: -76.426469, lat: 40.043914}, {lng: -76.427534, lat: 40.043722}, {lng: -76.427539, lat: 40.043721}, {lng: -76.427581, lat: 40.043824}, {lng: -76.427696, lat: 40.044108}, {lng: -76.427697, lat: 40.04411}, {lng: -76.428611, lat: 40.043923}, {lng: -76.428622, lat: 40.04392}, {lng: -76.428569, lat: 40.043834}, {lng: -76.428459, lat: 40.043652}, {lng: -76.428482, lat: 40.043645}, {lng: -76.428498, lat: 40.043641}, {lng: -76.428577, lat: 40.043619}, {lng: -76.429262, lat: 40.043429}, {lng: -76.429283, lat: 40.043423}, {lng: -76.429286, lat: 40.043422}, {lng: -76.429544, lat: 40.04335}, {lng: -76.429484, lat: 40.043198}, {lng: -76.430003, lat: 40.043075}, {lng: -76.430176, lat: 40.043588}, {lng: -76.430197, lat: 40.043651}, {lng: -76.430198, lat: 40.04365}, {lng: -76.434264, lat: 40.042515}, {lng: -76.434666, lat: 40.042402}, {lng: -76.434796, lat: 40.042618}, {lng: -76.434855, lat: 40.042716}, {lng: -76.434943, lat: 40.042862}, {lng: -76.434949, lat: 40.042873}, {lng: -76.434952, lat: 40.042878}, {lng: -76.434956, lat: 40.042884}, {lng: -76.435337, lat: 40.04357}, {lng: -76.435346, lat: 40.043576}, {lng: -76.435422, lat: 40.043635}, {lng: -76.435439, lat: 40.043656}, {lng: -76.435587, lat: 40.043828}, {lng: -76.43566, lat: 40.043913}, {lng: -76.435744, lat: 40.044011}, {lng: -76.435869, lat: 40.044159}, {lng: -76.435909, lat: 40.044234}, {lng: -76.435959, lat: 40.044321}, {lng: -76.436391, lat: 40.045289}, {lng: -76.436518, lat: 40.045702}, {lng: -76.436854, lat: 40.045567}, {lng: -76.437363, lat: 40.045363}, {lng: -76.4376, lat: 40.045268}, {lng: -76.437642, lat: 40.045251}, {lng: -76.437684, lat: 40.045235}, {lng: -76.43822, lat: 40.045022}, {lng: -76.438261, lat: 40.045006}, {lng: -76.438261, lat: 40.045007}
    ];
    var magistrate02201Area = [
        {lng: -76.3034622, lat: 40.026077}, {lng: -76.298597, lat: 40.024081}, {lng: -76.291028, lat: 40.023856}, {lng: -76.281347, lat: 40.006979}, {lng: -76.2779, lat: 40.008936}, {lng: -76.283954, lat: 40.019461}, {lng: -76.278901, lat: 40.021903}, {lng: -76.279174, lat: 40.024963}, {lng: -76.2808946, lat: 40.0264041}, {lng: -76.2779465, lat: 40.0251593}, {lng: -76.2775304, lat: 40.0261163}, {lng: -76.2741944, lat: 40.0260936}, {lng: -76.2710516, lat: 40.0257259}, {lng: -76.2666541, lat: 40.0264363}, {lng: -76.2578587, lat: 40.0289413}, {lng: -76.2602232, lat: 40.0331299}, {lng: -76.2595449, lat: 40.0341137}, {lng: -76.2594566, lat: 40.0354918}, {lng: -76.2588747, lat: 40.0368042}, {lng: -76.2627702, lat: 40.0371254}, {lng: -76.2657551, lat: 40.0352046}, {lng: -76.2690644, lat: 40.0344189}, {lng: -76.2694174, lat: 40.033406}, {lng: -76.2687073, lat: 40.0322015}, {lng: -76.2662139, lat: 40.0327498}, {lng: -76.2637591, lat: 40.0292457}, {lng: -76.2674588, lat: 40.027322}, {lng: -76.2687429, lat: 40.0281739}, {lng: -76.279465, lat: 40.028662}, {lng: -76.2793658, lat: 40.0292111}, {lng: -76.2784807, lat: 40.0298493}, {lng: -76.2780837, lat: 40.0327358}, {lng: -76.2781051, lat: 40.0334485}, {lng: -76.2795213, lat: 40.0331979}, {lng: -76.2799075, lat: 40.0322697}, {lng: -76.2815597, lat: 40.0342658}, {lng: -76.2864736, lat: 40.0373627}, {lng: -76.2869564, lat: 40.0386391}, {lng: -76.2870154, lat: 40.0393511}, {lng: -76.2871603, lat: 40.0400057}, {lng: -76.2891988, lat: 40.0399873}, {lng: -76.2942842, lat: 40.0392767}, {lng: -76.3000347, lat: 40.0385918}, {lng: -76.305785, lat: 40.0378984}, {lng: -76.3044733, lat: 40.0313798}, {lng: -76.3034622, lat: 40.026077}
    ];
    var magistrate02202Area = [
        {lng: -76.3083544, lat: 40.0521251}, {lng: -76.3117631, lat: 40.0518413}, {lng: -76.3129525, lat: 40.0476257}, {lng: -76.3129249, lat: 40.0467988}, {lng: -76.3142276, lat: 40.0463662}, {lng: -76.3137429, lat: 40.0436274}, {lng: -76.3169495, lat: 40.0433198}, {lng: -76.316754, lat: 40.0417191}, {lng: -76.3204824, lat: 40.041409}, {lng: -76.3248491, lat: 40.0423002}, {lng: -76.325171, lat: 40.0413885}, {lng: -76.325729, lat: 40.0400252}, {lng: -76.3263545, lat: 40.0385388}, {lng: -76.3236325, lat: 40.038268}, {lng: -76.3185469, lat: 40.0378246}, {lng: -76.3152478, lat: 40.0369579}, {lng: -76.305785, lat: 40.0378984}, {lng: -76.3083544, lat: 40.0521251}
    ];
    var magistrate02203Area = [
        {lng: -76.3019491, lat: 40.0095648}, {lng: -76.305492, lat: 40.017292}, {lng: -76.304199, lat: 40.021578}, {lng: -76.302419, lat: 40.020996}, {lng: -76.298984, lat: 40.016568}, {lng: -76.294647, lat: 40.016477}, {lng: -76.2938219, lat: 40.0184787}, {lng: -76.3034622, lat: 40.026077}, {lng: -76.3048784, lat: 40.022725}, {lng: -76.3152211, lat: 40.0177952}, {lng: -76.3197701, lat: 40.0205559}, {lng: -76.3214009, lat: 40.0187155}, {lng: -76.3238041, lat: 40.0207531}, {lng: -76.32286, lat: 40.0215419}, {lng: -76.3243191, lat: 40.0226593}, {lng: -76.3234608, lat: 40.0235795}, {lng: -76.3250057, lat: 40.0247625}, {lng: -76.3220017, lat: 40.0273916}, {lng: -76.3231175, lat: 40.0296918}, {lng: -76.3245337, lat: 40.0297904}, {lng: -76.324877, lat: 40.0321235}, {lng: -76.3236325, lat: 40.038268}, {lng: -76.3263545, lat: 40.0385388}, {lng: -76.3248491, lat: 40.0423002}, {lng: -76.3204824, lat: 40.041409}, {lng: -76.316754, lat: 40.0417191}, {lng: -76.3169495, lat: 40.0433198}, {lng: -76.3137429, lat: 40.0436274}, {lng: -76.3142276, lat: 40.0463662}, {lng: -76.3129249, lat: 40.0467988}, {lng: -76.3117631, lat: 40.0518413}, {lng: -76.313546, lat: 40.052132}, {lng: -76.3134708, lat: 40.0554}, {lng: -76.3127035, lat: 40.0595114}, {lng: -76.3135239, lat: 40.061898}, {lng: -76.314541, lat: 40.0644236}, {lng: -76.3156654, lat: 40.0642066}, {lng: -76.3165625, lat: 40.0659241}, {lng: -76.315374, lat: 40.0665673}, {lng: -76.3160655, lat: 40.0682149}, {lng: -76.3180912, lat: 40.0701758}, {lng: -76.3183732, lat: 40.0710068}, {lng: -76.3237222, lat: 40.0695628}, {lng: -76.3252342, lat: 40.0730018}, {lng: -76.3269463, lat: 40.0719858}, {lng: -76.3256273, lat: 40.0688565}, {lng: -76.3285922, lat: 40.0672652}, {lng: -76.3285283, lat: 40.0664118}, {lng: -76.331011, lat: 40.0659103}, {lng: -76.3319552, lat: 40.0656598}, {lng: -76.3330882, lat: 40.0667738}, {lng: -76.3345113, lat: 40.0683198}, {lng: -76.3355212, lat: 40.0686858}, {lng: -76.3364902, lat: 40.0696118}, {lng: -76.3387472, lat: 40.0713598}, {lng: -76.3408542, lat: 40.0726698}, {lng: -76.3417452, lat: 40.0725688}, {lng: -76.3421323, lat: 40.0707478}, {lng: -76.3435922, lat: 40.0671098}, {lng: -76.345266, lat: 40.067275}, {lng: -76.3460082, lat: 40.0656248}, {lng: -76.3438312, lat: 40.0653668}, {lng: -76.3422122, lat: 40.0649708}, {lng: -76.3417835, lat: 40.064802}, {lng: -76.3381675, lat: 40.0627241}, {lng: -76.3368576, lat: 40.0617215}, {lng: -76.3335082, lat: 40.0583696}, {lng: -76.3302952, lat: 40.0565618}, {lng: -76.3299952, lat: 40.0563828}, {lng: -76.3291363, lat: 40.0571868}, {lng: -76.3266593, lat: 40.0571158}, {lng: -76.3231445, lat: 40.0561211}, {lng: -76.3234538, lat: 40.0536337}, {lng: -76.3237132, lat: 40.0531598}, {lng: -76.3222242, lat: 40.0536178}, {lng: -76.3212343, lat: 40.0547738}, {lng: -76.3197672, lat: 40.0535858}, {lng: -76.3209653, lat: 40.0528568}, {lng: -76.3206933, lat: 40.0521928}, {lng: -76.3212842, lat: 40.0516768}, {lng: -76.3200882, lat: 40.0511218}, {lng: -76.3213932, lat: 40.0513628}, {lng: -76.3214903, lat: 40.0511591}, {lng: -76.3278699, lat: 40.0507391}, {lng: -76.3404022, lat: 40.0529178}, {lng: -76.3435838, lat: 40.0477715}, {lng: -76.34432, lat: 40.046151}, {lng: -76.34532, lat: 40.044512}, {lng: -76.344989, lat: 40.043331}, {lng: -76.3427462, lat: 40.0415369}, {lng: -76.342611, lat: 40.039779}, {lng: -76.342744, lat: 40.038608}, {lng: -76.343064, lat: 40.03769}, {lng: -76.3421873, lat: 40.0314757}, {lng: -76.343977, lat: 40.029055}, {lng: -76.343308, lat: 40.027065}, {lng: -76.3527823, lat: 40.0230075}, {lng: -76.3488615, lat: 40.0199153}, {lng: -76.3453483, lat: 40.0176772}, {lng: -76.3434718, lat: 40.0186239}, {lng: -76.3412635, lat: 40.0194327}, {lng: -76.339851, lat: 40.0185523}, {lng: -76.3391719, lat: 40.0186976}, {lng: -76.3381141, lat: 40.0175422}, {lng: -76.3344541, lat: 40.0131932}, {lng: -76.3363612, lat: 40.0119891}, {lng: -76.3401753, lat: 40.0100414}, {lng: -76.342997, lat: 40.0088415}, {lng: -76.3443059, lat: 40.0082497}, {lng: -76.3467519, lat: 40.0071319}, {lng: -76.3423753, lat: 39.9987825}, {lng: -76.3283469, lat: 39.987683}, {lng: -76.3277078, lat: 39.9995989}, {lng: -76.3317515, lat: 40.0088327}, {lng: -76.331084, lat: 40.0108638}, {lng: -76.329472, lat: 40.0119088}, {lng: -76.3270018, lat: 40.0104557}, {lng: -76.3251887, lat: 40.0039844}, {lng: -76.3217342, lat: 39.999456}, {lng: -76.320661, lat: 40.0056535}, {lng: -76.31397, lat: 40.005021}, {lng: -76.310373, lat: 39.996343}, {lng: -76.3078714, lat: 39.9960866}, {lng: -76.3019491, lat: 40.0095648}
    ];
    var magistrate02204Area = [
        {lng: -76.305785, lat: 40.0378984}, {lng: -76.2871603, lat: 40.0400057}, {lng: -76.2866655, lat: 40.0434372}, {lng: -76.2835956, lat: 40.044963}, {lng: -76.2836357, lat: 40.0440066}, {lng: -76.2776218, lat: 40.0443276}, {lng: -76.2829317, lat: 40.0468753}, {lng: -76.2841103, lat: 40.0477656}, {lng: -76.2858667, lat: 40.0513203}, {lng: -76.2642416, lat: 40.043185}, {lng: -76.2640748, lat: 40.0422769}, {lng: -76.2630546, lat: 40.0413807}, {lng: -76.2628168, lat: 40.039654}, {lng: -76.2590369, lat: 40.0403073}, {lng: -76.2626772, lat: 40.0458849}, {lng: -76.2553237, lat: 40.0468875}, {lng: -76.256066, lat: 40.0487285}, {lng: -76.2560482, lat: 40.0501436}, {lng: -76.2554976, lat: 40.0512655}, {lng: -76.2546108, lat: 40.0522608}, {lng: -76.2597464, lat: 40.0544491}, {lng: -76.2626363, lat: 40.0550154}, {lng: -76.26906, lat: 40.0546043}, {lng: -76.2722087, lat: 40.053585}, {lng: -76.2718519, lat: 40.0528701}, {lng: -76.2757866, lat: 40.051301}, {lng: -76.2762317, lat: 40.0526633}, {lng: -76.2787418, lat: 40.0523639}, {lng: -76.2786121, lat: 40.0559041}, {lng: -76.2798979, lat: 40.0572689}, {lng: -76.2808386, lat: 40.0578305}, {lng: -76.2821623, lat: 40.0569828}, {lng: -76.2861401, lat: 40.0543016}, {lng: -76.2892032, lat: 40.0520924}, {lng: -76.2886348, lat: 40.0541125}, {lng: -76.289941, lat: 40.0548519}, {lng: -76.2906248, lat: 40.0540145}, {lng: -76.295769, lat: 40.0540807}, {lng: -76.2967448, lat: 40.0548043}, {lng: -76.2982241, lat: 40.0534921}, {lng: -76.301569, lat: 40.0539226}, {lng: -76.2971441, lat: 40.0590539}, {lng: -76.301126, lat: 40.0599547}, {lng: -76.3049697, lat: 40.0562707}, {lng: -76.3051898, lat: 40.0558661}, {lng: -76.3021535, lat: 40.0527907}, {lng: -76.3083544, lat: 40.0521251}, {lng: -76.305785, lat: 40.0378984}
    ];

        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 40.144128, lng: -76.311420},
            zoom: 9,
            scaleControl: true
        });
        bounds = new google.maps.LatLngBounds();
        google.maps.event.addListenerOnce(map, 'tilesloaded', function(evt) {

            bounds = map.getBounds();
        });
        marker = new google.maps.Marker({
            position: center
        });
        var input = /** @type {!HTMLInputElement} */(
            document.getElementById('pac-input'));
        var types = document.getElementById('type-selector');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);

        //Create the polygons
    magistrate02101 = new google.maps.Polygon({
        path: magistrate02101Area,
        geodesic: true,
        strokeColor: '#02314E',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02103 = new google.maps.Polygon({
        path: magistrate02103Area,
        geodesic: true,
        strokeColor: '#81CFFF',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02201 = new google.maps.Polygon({
        path: magistrate02201Area,
        geodesic: true,
        strokeColor: 'blue',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02202 = new google.maps.Polygon({
        path: magistrate02202Area,
        geodesic: true,
        strokeColor: '#CCD839',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02203 = new google.maps.Polygon({
        path: magistrate02203Area,
        geodesic: true,
        strokeColor: '#CB006F',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });
    magistrate02204 = new google.maps.Polygon({
        path: magistrate02204Area,
        geodesic: true,
        strokeColor: '#81CFFF',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        fillColor: '#B1AAA9',
        fillOpacity: 0.35
    });

    magistrate02101.setMap(map);
    magistrate02103.setMap(map);
    magistrate02201.setMap(map);
    magistrate02202.setMap(map);
    magistrate02203.setMap(map);
    magistrate02204.setMap(map);


        autocomplete.addListener('place_changed', function() {
            marker.setMap(null);
            var place = autocomplete.getPlace();
            newBounds = bounds;
            if (!place.geometry) {
                window.alert("Returned place contains no geometry");
                return;
            };

            houseNum =  place.address_components[0].long_name;
            streetName = place.address_components[1].long_name;
            town = place.address_components[2].long_name;
            county = place.address_components[3].long_name;
            state = place.address_components[4].short_name;
            zipcode = place.address_components[6].long_name;

            $('#state').val(state);
            $('#zipcode').val(zipcode);
            $('#county').val(county);
            $('#house_num').val(houseNum);
            $('#street_name').val(streetName);
            $('#town').val(town);

            marker.setPosition(place.geometry.location);
            marker.setMap(map);
            newBounds.extend(place.geometry.location);
            map.fitBounds(newBounds);

            if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02101)){
                $('#court_number').val('02-1-01');
                $('#court_address1').val('641 Union Street');
                $('#court_address2').val('Lancaster, PA 17603');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02103)){
                $('#court_number').val('02-1-03');
                $('#court_address1').val('341 Chestnut Street');
                $('#court_address2').val('Columbia, PA 17512');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02201)){
                $('#court_number').val('02-2-01');
                $('#court_address1').val('123 Locust St');
                $('#court_address2').val('Lancaster, PA 17602');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02202)){
                $('#court_number').val('02-2-02');
                $('#court_address1').val('150 N. Queen Street Suite 120');
                $('#court_address2').val('Lancaster, PA 17603');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02203)){
                $('#court_number').val('02-2-03');
                $('#court_address1').val('1351 Elm Ave');
                $('#court_address2').val('Lancaster, PA 17603');
            } else if (google.maps.geometry.poly.containsLocation(place.geometry.location, magistrate02201)){
                $('#court_number').val('02-2-04');
                $('#court_address1').val('796-A New Holland Ave');
                $('#court_address2').val('Lancaster, PA 17602');
            } else {
                alert('The address is outside of all areas.');
            }
        });



    $(window).keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    $('input[type=radio][name=rented_by]').change(function(){
       console.log($(this)[0].id);
       if ($(this)[0].id == 'rented_by_other') {
           $('#landlord').prop('hidden', false);
       } else {
           $('#landlord').prop('hidden', true);
       }
    });

    //On Submit gather variables and make ajax call to backend

    $('#pdf_download_btn').on('click', function() {
       var data = $('#eviction_form').serialize();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            },
            type: "POST",
            url: '/online-eviction/pdf-data',
            dataType: 'json',
            data: data,

            success: function (data) {
                console.log(data);
                //location.reload();
            },
            error: function (data) {
                console.log(data);
            }
        });
    });


});