#!/usr/bin/env Rscript
args = commandArgs(trailingOnly=TRUE)

if (length(args) != 0) {
    library(rvest)
    library(stringr)

    html <- read_html(args)
    content <- html %>% html_node('#mw-content-text')
    content <- str_replace_all(content,"<.*?>","")
    content <- str_replace_all(content,"\\n+"," ")

    writeLines(content,file('/tmp/wili-crawl.txt'))   
}