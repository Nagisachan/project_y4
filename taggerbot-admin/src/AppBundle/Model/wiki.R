#!/usr/bin/env Rscript
args = commandArgs(trailingOnly=TRUE)

if (length(args) != 0) {
    url <- args[1]
    file_path <- args[2]

    print("start crawling...")

    library(stringr)
    library(rvest)

    html <- read_html(url)
    content <- html %>% html_node('#mw-content-text')
    content <- str_replace_all(content,"<.*?>","")
    # content <- str_replace_all(content,"\\n+","|")

    writeLines(content,file(file_path))

    print('done crawling...')   
}