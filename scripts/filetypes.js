var filetypeDatabase = [
    {
        "category" : "audio",
        "filetypes" : ["aac","aif","aiff","amr","au","flac","m4a","m4p","mid","mp3","ogg","oga","ra","wav","wma"],
        "color" : "white"
    },
    {
        "category" : "picture",
        "filetypes" : ["bmp","eps","gif","ico","jpg","jpeg","png","tga","tif","tiff","psd","svg"],
        "color" : "white"
    },
    {
        "category" : "video",
        "filetypes" : ["3gp","avi","flv","mkv","mov","mp4","mpg","mpeg","mpv","m2v","m4v","ogv","qt","wmv"],
        "color" : "white"
    },
    {
        "category" : "archive",
        "filetypes" : ["7z","ace","arj","bz2","cab","gz","rar","tar","zip"],
        "color" : "darkyellow"
    },
    {
        "category" : "image",
        "filetypes" : ["iso","nrg","img","dsk","dmg","mdx"],
        "color" : "darkyellow"
    },
    {
        "category" : "msword",
        "filetypes" : ["doc","docx","docm"],
        "color" : "white"
    },
    {
        "category" : "msexcel",
        "filetypes" : ["xls","xlsx","xlsm"],
        "color" : "white"
    },
    {
        "category" : "mspowerpoint",
        "filetypes" : ["ppt","pptx"],
        "color" : "white"
    },
    {
        "category" : "xml",
        "filetypes" : ["xml","html"],
        "color" : "darkblue"
    },
    {
        "category" : "php",
        "filetypes" : ["php","phar"],
        "color" : "darkblue"
    },
    {
        "category" : "python",
        "filetypes" : ["py","pyc"],
        "color" : "darkblue"
    },
    {
        "category" : "ruby",
        "filetypes" : ["rb"],
        "color" : "darkblue"
    },
    {
        "category" : "runnable",
        "filetypes" : ["com","exe","msi","cmd","bat","sh"],
        "color" : "darkblue"
    },
    {
        "category" : "java",
        "filetypes" : ["java","js","class"],
        "color" : "darkblue"
    },
    {
        "category" : "database",
        "filetypes" : ["db","sql","mdb","mda"],
        "color" : "darkblue"
    },
    {
        "category" : "css",
        "filetypes" : ["css"],
        "color" : "darkblue"
    },
    {
        "category" : "deb",
        "filetypes" : ["deb"],
        "color" : "pink"
    },
    {
        "category" : "pdf",
        "filetypes" : ["pdf"],
        "color" : "white"
    },
    {
        "category" : "oowriter",
        "filetypes" : ["odt","ott"],
        "color" : "white"
    },
    {
        "category" : "oocalc",
        "filetypes" : ["ods","ots"],
        "color" : "white"
    },
    {
        "category" : "ooimpress",
        "filetypes" : ["odp","otp"],
        "color" : "white"
    }
];

function getFileCategory(filetype){
    if(filetype=="_folder") return "_folder";
    if(filetype=="_folderback") return "_folderback";

    for(var i=0;i<filetypeDatabase.length;i++){
        for(var j=0;j<filetypeDatabase[i].filetypes.length;j++){
            if(filetypeDatabase[i].filetypes[j]==filetype){
                return filetypeDatabase[i].category;
            }
        }
    }
    return "_blank";
}

function getFileTextColor(filetype){
    for(var i=0;i<filetypeDatabase.length;i++){
        for(var j=0;j<filetypeDatabase[i].filetypes.length;j++){
            if(filetypeDatabase[i].filetypes[j]==filetype){
                return filetypeDatabase[i].color;
            }
        }
    }
    return "darkblue";
}

function fileTypeTextFormatter(filetype){
    if(filetype=="_FOLDER") return "";
    if(filetype=="_FOLDERBACK") return "";
    return filetype;
}