import java.io.*;
import java.util.Scanner;

public class PDFSplitter {

    public static void main(String[] args) throws IOException {
        if (args.length != 2) {
            System.out.println("Usage: PDFSplitter <input text file> <output text file>");
            System.exit(-1);
        }
       
        File fileDir = new File(args[0]);
        String content = new Scanner(fileDir).useDelimiter("\\Z").next();
        int language;
        if (content.contains("ก"))
            language = 1;
        else
            language = 0;

        BufferedReader in = new BufferedReader(new InputStreamReader(new FileInputStream(fileDir), "UTF8"));
        String str;
        FileWriter fw = new FileWriter(args[1]);
        BufferedWriter bw = new BufferedWriter(fw);
        StringBuilder paragraph = new StringBuilder();
        while ((str = in.readLine()) != null) {
            if (!str.trim().isEmpty()) {
                //check last char of each line
                //	System.out.println("Last char of line " + i + ": "+str.charAt(str.length() - 1));
                //int ascii = str.charAt(str.length() - 1);
                //		System.out.println("ASCII: " + ascii );
                if (language == 1) {
                    //replace สระอำ
                    str = str.replaceAll(" า", "ำ");
                    str = str.replaceAll(" ่า", "่ำ");
                    str = str.replaceAll(" ้า", "้ำ");
                    str = str.replaceAll(" ๊า", "๊ำ");
                    str = str.replaceAll(" ๋า", "๋ำ");
                    //str = str.replaceAll("\"","");

                    //System.out.println(str);
                    paragraph.append(str);
                    if (str.charAt(str.length() - 1) == ' ') {
                        writeToFile(paragraph, bw);
                    }
                } else if (language == 0) {
                    paragraph.append(str);
                    if (str.charAt(str.length() - 1) == ' ' && str.charAt(str.length() - 2) == '.') {
                        writeToFile(paragraph, bw);
                    }
                }
            }
        }

        if (paragraph.length() != 0) {
            writeToFile(paragraph, bw);
        }

        in.close();
        bw.close();
    }

    private static void writeToFile(StringBuilder paragraph, BufferedWriter bw) throws IOException {
        String insert = new String();
        insert = paragraph.toString();
        bw.write(insert);
        bw.write("\n");
        paragraph.setLength(0);
    }

}
