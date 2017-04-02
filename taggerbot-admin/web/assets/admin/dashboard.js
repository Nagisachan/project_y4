Morris.Bar({
    element: 'model-vs-f1',
    data: [
        { tag: 'Active', f1: 0.8 },
        { tag: 'PBL', f1: 0.75 },
        { tag: 'TF-Teacher', f1: 0.7 },
        { tag: 'TF-Student', f1: 0.75 },
        { tag: 'Teacher', f1: 0.85 },
        { tag: 'Student', f1: 0.65 },
        { tag: 'Math', f1: 0.72 },
        { tag: 'Science', f1: 0.65 },
    ],
    xkey: 'tag',
    ykeys: ['f1'],
    labels: ['F1'],
    barColors: ['#DB2828']
});

Morris.Line({
    element: 'doc-vs-parag',
    data: [
        { year: '2008', document: 5, paragraph: 50 },
        { year: '2009', document: 10, paragraph: 150 },
        { year: '2010', document: 30, paragraph: 300 },
        { year: '2011', document: 35, paragraph: 350 },
        { year: '2012', document: 50, paragraph: 450 }
    ],
    xkey: 'year',
    ykeys: ['document', 'paragraph'],
    labels: ['#Document', '#Paragraph'],
    lineColors: ['#008080', '#0E6EB8']
});

Morris.Bar({
    element: 'tag-vs-parag',
    data: [
        { tag: 'Active Learning', document: 20, paragraph: 90 },
        { tag: 'PBL', document: 10, paragraph: 65 },
        { tag: 'Trans Teacher', document: 7, paragraph: 40 },
        { tag: 'Trans Student', document: 13, paragraph: 65 },
        { tag: 'Teacher', document: 25, paragraph: 40 },
        { tag: 'Student', document: 20, paragraph: 65 },
        { tag: 'Math', document: 5, paragraph: 90 }
    ],
    xkey: 'tag',
    ykeys: ['document', 'paragraph'],
    labels: ['#Document', '#Paragraph'],
    barColors: ['#008080', '#0E6EB8']
});

Morris.Donut({
    element: 'manual-vs-auto',
    data: [
        { label: "Manual Tagged", value: 12 },
        { label: "Auto Tagged", value: 30 },
        { label: "Not tagged", value: 20 }
    ],
    colors: ['#1678c2', '#008080', '#DB2828']
});