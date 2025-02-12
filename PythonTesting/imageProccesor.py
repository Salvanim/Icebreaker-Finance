from PIL import Image
import re
import tkinter as tk
from tkinter import filedialog

class ImageProcessor:
    def __init__(self, image_path="", imageSize=(), encoded_char_set="", round=0, mode='RGBA'):
        #defines pregenerated dictionarys for rgba
        #self.characterDictionary = {0: {0: '一', 1: '丁', 2: '丂', 3: '七', 4: '丄', 5: '丅', 6: '丆', 7: '万', 8: '丈', 9: '三', 10: '上', 11: '下', 12: '丌', 13: '不', 14: '与', 15: '丏', 16: '丐', 17: '丑', 18: '丒', 19: '专', 20: '且', 21: '丕', 22: '世', 23: '丗', 24: '丘', 25: '丙', 26: '业', 27: '丛', 28: '东', 29: '丝', 30: '丞', 31: '丟', 32: '丠', 33: '両', 34: '丢', 35: '丣', 36: '两', 37: '严', 38: '並', 39: '丧', 40: '丨', 41: '丩', 42: '个', 43: '丫', 44: '丬', 45: '中', 46: '丮', 47: '丯', 48: '丰', 49: '丱', 50: '串', 51: '丳', 52: '临', 53: '丵', 54: '丶', 55: '丷', 56: '丸', 57: '丹', 58: '为', 59: '主', 60: '丼', 61: '丽', 62: '举', 63: '丿', 64: '乀', 65: '乁', 66: '乂', 67: '乃', 68: '乄', 69: '久', 70: '乆', 71: '乇', 72: '么', 73: '义', 74: '乊', 75: '之', 76: '乌', 77: '乍', 78: '乎', 79: '乏', 80: '乐', 81: '乑', 82: '乒', 83: '乓', 84: '乔', 85: '乕', 86: '乖', 87: '乗', 88: '乘', 89: '乙', 90: '乚', 91: '乛', 92: '乜', 93: '九', 94: '乞', 95: '也', 96: '习', 97: '乡', 98: '乢', 99: '乣', 100: '乤', 101: '乥', 102: '书', 103: '乧', 104: '乨', 105: '乩', 106: '乪', 107: '乫', 108: '乬', 109: '乭', 110: '乮', 111: '乯', 112: '买', 113: '乱', 114: '乲', 115: '乳', 116: '乴', 117: '乵', 118: '乶', 119: '乷', 120: '乸', 121: '乹', 122: '乺', 123: '乻', 124: '乼', 125: '乽', 126: '乾', 127: '乿', 128: '亀', 129: '亁', 130: '亂', 131: '亃', 132: '亄', 133: '亅', 134: '了', 135: '亇', 136: '予', 137: '争', 138: '亊', 139: '事', 140: '二', 141: '亍', 142: '于', 143: '亏', 144: '亐', 145: '云', 146: '互', 147: '亓', 148: '五', 149: '井', 150: '亖', 151: '亗', 152: '亘', 153: '亙', 154: '亚', 155: '些', 156: '亜', 157: '亝', 158: '亞', 159: '亟', 160: '亠', 161: '亡', 162: '亢', 163: '亣', 164: '交', 165: '亥', 166: '亦', 167: '产', 168: '亨', 169: '亩', 170: '亪', 171: '享', 172: '京', 173: '亭', 174: '亮', 175: '亯', 176: '亰', 177: '亱', 178: '亲', 179: '亳', 180: '亴', 181: '亵', 182: '亶', 183: '亷', 184: '亸', 185: '亹', 186: '人', 187: '亻', 188: '亼', 189: '亽', 190: '亾', 191: '亿', 192: '什', 193: '仁', 194: '仂', 195: '仃', 196: '仄', 197: '仅', 198: '仆', 199: '仇', 200: '仈', 201: '仉', 202: '今', 203: '介', 204: '仌', 205: '仍', 206: '从', 207: '仏', 208: '仐', 209: '仑', 210: '仒', 211: '仓', 212: '仔', 213: '仕', 214: '他', 215: '仗', 216: '付', 217: '仙', 218: '仚', 219: '仛', 220: '仜', 221: '仝', 222: '仞', 223: '仟', 224: '仠', 225: '仡', 226: '仢', 227: '代', 228: '令', 229: '以', 230: '仦', 231: '仧', 232: '仨', 233: '仩', 234: '仪', 235: '仫', 236: '们', 237: '仭', 238: '仮', 239: '仯', 240: '仰', 241: '仱', 242: '仲', 243: '仳', 244: '仴', 245: '仵', 246: '件', 247: '价', 248: '仸', 249: '仹', 250: '仺', 251: '任', 252: '仼', 253: '份', 254: '仾', 255: '仿'}, 1: {0: '伀', 1: '企', 2: '伂', 3: '伃', 4: '伄', 5: '伅', 6: '伆', 7: '伇', 8: '伈', 9: '伉', 10: '伊', 11: '伋', 12: '伌', 13: '伍', 14: '伎', 15: '伏', 16: '伐', 17: '休', 18: '伒', 19: '伓', 20: '伔', 21: '伕', 22: '伖', 23: '众', 24: '优', 25: '伙', 26: '会', 27: '伛', 28: '伜', 29: '伝', 30: '伞', 31: '伟', 32: '传', 33: '伡', 34: '伢', 35: '伣', 36: '伤', 37: '伥', 38: '伦', 39: '伧', 40: '伨', 41: '伩', 42: '伪', 43: '伫', 44: '伬', 45: '伭', 46: '伮', 47: '伯', 48: '估', 49: '伱', 50: '伲', 51: '伳', 52: '伴', 53: '伵', 54: '伶', 55: '伷', 56: '伸', 57: '伹', 58: '伺', 59: '伻', 60: '似', 61: '伽', 62: '伾', 63: '伿', 64: '佀', 65: '佁', 66: '佂', 67: '佃', 68: '佄', 69: '佅', 70: '但', 71: '佇', 72: '佈', 73: '佉', 74: '佊', 75: '佋', 76: '佌', 77: '位', 78: '低', 79: '住', 80: '佐', 81: '佑', 82: '佒', 83: '体', 84: '佔', 85: '何', 86: '佖', 87: '佗', 88: '佘', 89: '余', 90: '佚', 91: '佛', 92: '作', 93: '佝', 94: '佞', 95: '佟', 96: '你', 97: '佡', 98: '佢', 99: '佣', 100: '佤', 101: '佥', 102: '佦', 103: '佧', 104: '佨', 105: '佩', 106: '佪', 107: '佫', 108: '佬', 109: '佭', 110: '佮', 111: '佯', 112: '佰', 113: '佱', 114: '佲', 115: '佳', 116: '佴', 117: '併', 118: '佶', 119: '佷', 120: '佸', 121: '佹', 122: '佺', 123: '佻', 124: '佼', 125: '佽', 126: '佾', 127: '使', 128: '侀', 129: '侁', 130: '侂', 131: '侃', 132: '侄', 133: '侅', 134: '來', 135: '侇', 136: '侈', 137: '侉', 138: '侊', 139: '例', 140: '侌', 141: '侍', 142: '侎', 143: '侏', 144: '侐', 145: '侑', 146: '侒', 147: '侓', 148: '侔', 149: '侕', 150: '侖', 151: '侗', 152: '侘', 153: '侙', 154: '侚', 155: '供', 156: '侜', 157: '依', 158: '侞', 159: '侟', 160: '侠', 161: '価', 162: '侢', 163: '侣', 164: '侤', 165: '侥', 166: '侦', 167: '侧', 168: '侨', 169: '侩', 170: '侪', 171: '侫', 172: '侬', 173: '侭', 174: '侮', 175: '侯', 176: '侰', 177: '侱', 178: '侲', 179: '侳', 180: '侴', 181: '侵', 182: '侶', 183: '侷', 184: '侸', 185: '侹', 186: '侺', 187: '侻', 188: '侼', 189: '侽', 190: '侾', 191: '便', 192: '俀', 193: '俁', 194: '係', 195: '促', 196: '俄', 197: '俅', 198: '俆', 199: '俇', 200: '俈', 201: '俉', 202: '俊', 203: '俋', 204: '俌', 205: '俍', 206: '俎', 207: '俏', 208: '俐', 209: '俑', 210: '俒', 211: '俓', 212: '俔', 213: '俕', 214: '俖', 215: '俗', 216: '俘', 217: '俙', 218: '俚', 219: '俛', 220: '俜', 221: '保', 222: '俞', 223: '俟', 224: '俠', 225: '信', 226: '俢', 227: '俣', 228: '俤', 229: '俥', 230: '俦', 231: '俧', 232: '俨', 233: '俩', 234: '俪', 235: '俫', 236: '俬', 237: '俭', 238: '修', 239: '俯', 240: '俰', 241: '俱', 242: '俲', 243: '俳', 244: '俴', 245: '俵', 246: '俶', 247: '俷', 248: '俸', 249: '俹', 250: '俺', 251: '俻', 252: '俼', 253: '俽', 254: '俾', 255: '俿'}, 2: {0: '倀', 1: '倁', 2: '倂', 3: '倃', 4: '倄', 5: '倅', 6: '倆', 7: '倇', 8: '倈', 9: '倉', 10: '倊', 11: '個', 12: '倌', 13: '倍', 14: '倎', 15: '倏', 16: '倐', 17: '們', 18: '倒', 19: '倓', 20: '倔', 21: '倕', 22: '倖', 23: '倗', 24: '倘', 25: '候', 26: '倚', 27: '倛', 28: '倜', 29: '倝', 30: '倞', 31: '借', 32: '倠', 33: '倡', 34: '倢', 35: '倣', 36: '値', 37: '倥', 38: '倦', 39: '倧', 40: '倨', 41: '倩', 42: '倪', 43: '倫', 44: '倬', 45: '倭', 46: '倮', 47: '倯', 48: '倰', 49: '倱', 50: '倲', 51: '倳', 52: '倴', 53: '倵', 54: '倶', 55: '倷', 56: '倸', 57: '倹', 58: '债', 59: '倻', 60: '值', 61: '倽', 62: '倾', 63: '倿', 64: '偀', 65: '偁', 66: '偂', 67: '偃', 68: '偄', 69: '偅', 70: '偆', 71: '假', 72: '偈', 73: '偉', 74: '偊', 75: '偋', 76: '偌', 77: '偍', 78: '偎', 79: '偏', 80: '偐', 81: '偑', 82: '偒', 83: '偓', 84: '偔', 85: '偕', 86: '偖', 87: '偗', 88: '偘', 89: '偙', 90: '做', 91: '偛', 92: '停', 93: '偝', 94: '偞', 95: '偟', 96: '偠', 97: '偡', 98: '偢', 99: '偣', 100: '偤', 101: '健', 102: '偦', 103: '偧', 104: '偨', 105: '偩', 106: '偪', 107: '偫', 108: '偬', 109: '偭', 110: '偮', 111: '偯', 112: '偰', 113: '偱', 114: '偲', 115: '偳', 116: '側', 117: '偵', 118: '偶', 119: '偷', 120: '偸', 121: '偹', 122: '偺', 123: '偻', 124: '偼', 125: '偽', 126: '偾', 127: '偿', 128: '傀', 129: '傁', 130: '傂', 131: '傃', 132: '傄', 133: '傅', 134: '傆', 135: '傇', 136: '傈', 137: '傉', 138: '傊', 139: '傋', 140: '傌', 141: '傍', 142: '傎', 143: '傏', 144: '傐', 145: '傑', 146: '傒', 147: '傓', 148: '傔', 149: '傕', 150: '傖', 151: '傗', 152: '傘', 153: '備', 154: '傚', 155: '傛', 156: '傜', 157: '傝', 158: '傞', 159: '傟', 160: '傠', 161: '傡', 162: '傢', 163: '傣', 164: '傤', 165: '傥', 166: '傦', 167: '傧', 168: '储', 169: '傩', 170: '傪', 171: '傫', 172: '催', 173: '傭', 174: '傮', 175: '傯', 176: '傰', 177: '傱', 178: '傲', 179: '傳', 180: '傴', 181: '債', 182: '傶', 183: '傷', 184: '傸', 185: '傹', 186: '傺', 187: '傻', 188: '傼', 189: '傽', 190: '傾', 191: '傿', 192: '僀', 193: '僁', 194: '僂', 195: '僃', 196: '僄', 197: '僅', 198: '僆', 199: '僇', 200: '僈', 201: '僉', 202: '僊', 203: '僋', 204: '僌', 205: '働', 206: '僎', 207: '像', 208: '僐', 209: '僑', 210: '僒', 211: '僓', 212: '僔', 213: '僕', 214: '僖', 215: '僗', 216: '僘', 217: '僙', 218: '僚', 219: '僛', 220: '僜', 221: '僝', 222: '僞', 223: '僟', 224: '僠', 225: '僡', 226: '僢', 227: '僣', 228: '僤', 229: '僥', 230: '僦', 231: '僧', 232: '僨', 233: '僩', 234: '僪', 235: '僫', 236: '僬', 237: '僭', 238: '僮', 239: '僯', 240: '僰', 241: '僱', 242: '僲', 243: '僳', 244: '僴', 245: '僵', 246: '僶', 247: '僷', 248: '僸', 249: '價', 250: '僺', 251: '僻', 252: '僼', 253: '僽', 254: '僾', 255: '僿'}, 3: {0: '儀', 1: '儁', 2: '儂', 3: '儃', 4: '億', 5: '儅', 6: '儆', 7: '儇', 8: '儈', 9: '儉', 10: '儊', 11: '儋', 12: '儌', 13: '儍', 14: '儎', 15: '儏', 16: '儐', 17: '儑', 18: '儒', 19: '儓', 20: '儔', 21: '儕', 22: '儖', 23: '儗', 24: '儘', 25: '儙', 26: '儚', 27: '儛', 28: '儜', 29: '儝', 30: '儞', 31: '償', 32: '儠', 33: '儡', 34: '儢', 35: '儣', 36: '儤', 37: '儥', 38: '儦', 39: '儧', 40: '儨', 41: '儩', 42: '優', 43: '儫', 44: '儬', 45: '儭', 46: '儮', 47: '儯', 48: '儰', 49: '儱', 50: '儲', 51: '儳', 52: '儴', 53: '儵', 54: '儶', 55: '儷', 56: '儸', 57: '儹', 58: '儺', 59: '儻', 60: '儼', 61: '儽', 62: '儾', 63: '儿', 64: '兀', 65: '允', 66: '兂', 67: '元', 68: '兄', 69: '充', 70: '兆', 71: '兇', 72: '先', 73: '光', 74: '兊', 75: '克', 76: '兌', 77: '免', 78: '兎', 79: '兏', 80: '児', 81: '兑', 82: '兒', 83: '兓', 84: '兔', 85: '兕', 86: '兖', 87: '兗', 88: '兘', 89: '兙', 90: '党', 91: '兛', 92: '兜', 93: '兝', 94: '兞', 95: '兟', 96: '兠', 97: '兡', 98: '兢', 99: '兣', 100: '兤', 101: '入', 102: '兦', 103: '內', 104: '全', 105: '兩', 106: '兪', 107: '八', 108: '公', 109: '六', 110: '兮', 111: '兯', 112: '兰', 113: '共', 114: '兲', 115: '关', 116: '兴', 117: '兵', 118: '其', 119: '具', 120: '典', 121: '兹', 122: '兺', 123: '养', 124: '兼', 125: '兽', 126: '兾', 127: '兿', 128: '冀', 129: '冁', 130: '冂', 131: '冃', 132: '冄', 133: '内', 134: '円', 135: '冇', 136: '冈', 137: '冉', 138: '冊', 139: '冋', 140: '册', 141: '再', 142: '冎', 143: '冏', 144: '冐', 145: '冑', 146: '冒', 147: '冓', 148: '冔', 149: '冕', 150: '冖', 151: '冗', 152: '冘', 153: '写', 154: '冚', 155: '军', 156: '农', 157: '冝', 158: '冞', 159: '冟', 160: '冠', 161: '冡', 162: '冢', 163: '冣', 164: '冤', 165: '冥', 166: '冦', 167: '冧', 168: '冨', 169: '冩', 170: '冪', 171: '冫', 172: '冬', 173: '冭', 174: '冮', 175: '冯', 176: '冰', 177: '冱', 178: '冲', 179: '决', 180: '冴', 181: '况', 182: '冶', 183: '冷', 184: '冸', 185: '冹', 186: '冺', 187: '冻', 188: '冼', 189: '冽', 190: '冾', 191: '冿', 192: '净', 193: '凁', 194: '凂', 195: '凃', 196: '凄', 197: '凅', 198: '准', 199: '凇', 200: '凈', 201: '凉', 202: '凊', 203: '凋', 204: '凌', 205: '凍', 206: '凎', 207: '减', 208: '凐', 209: '凑', 210: '凒', 211: '凓', 212: '凔', 213: '凕', 214: '凖', 215: '凗', 216: '凘', 217: '凙', 218: '凚', 219: '凛', 220: '凜', 221: '凝', 222: '凞', 223: '凟', 224: '几', 225: '凡', 226: '凢', 227: '凣', 228: '凤', 229: '凥', 230: '処', 231: '凧', 232: '凨', 233: '凩', 234: '凪', 235: '凫', 236: '凬', 237: '凭', 238: '凮', 239: '凯', 240: '凰', 241: '凱', 242: '凲', 243: '凳', 244: '凴', 245: '凵', 246: '凶', 247: '凷', 248: '凸', 249: '凹', 250: '出', 251: '击', 252: '凼', 253: '函', 254: '凾', 255: '凿'}}
        #self.valueDictionary =  {0: {'一': 0, '丁': 1, '丂': 2, '七': 3, '丄': 4, '丅': 5, '丆': 6, '万': 7, '丈': 8, '三': 9, '上': 10, '下': 11, '丌': 12, '不': 13, '与': 14, '丏': 15, '丐': 16, '丑': 17, '丒': 18, '专': 19, '且': 20, '丕': 21, '世': 22, '丗': 23, '丘': 24, '丙': 25, '业': 26, '丛': 27, '东': 28, '丝': 29, '丞': 30, '丟': 31, '丠': 32, '両': 33, '丢': 34, '丣': 35, '两': 36, '严': 37, '並': 38, '丧': 39, '丨': 40, '丩': 41, '个': 42, '丫': 43, '丬': 44, '中': 45, '丮': 46, '丯': 47, '丰': 48, '丱': 49, '串': 50, '丳': 51, '临': 52, '丵': 53, '丶': 54, '丷': 55, '丸': 56, '丹': 57, '为': 58, '主': 59, '丼': 60, '丽': 61, '举': 62, '丿': 63, '乀': 64, '乁': 65, '乂': 66, '乃': 67, '乄': 68, '久': 69, '乆': 70, '乇': 71, '么': 72, '义': 73, '乊': 74, '之': 75, '乌': 76, '乍': 77, '乎': 78, '乏': 79, '乐': 80, '乑': 81, '乒': 82, '乓': 83, '乔': 84, '乕': 85, '乖': 86, '乗': 87, '乘': 88, '乙': 89, '乚': 90, '乛': 91, '乜': 92, '九': 93, '乞': 94, '也': 95, '习': 96, '乡': 97, '乢': 98, '乣': 99, '乤': 100, '乥': 101, '书': 102, '乧': 103, '乨': 104, '乩': 105, '乪': 106, '乫': 107, '乬': 108, '乭': 109, '乮': 110, '乯': 111, '买': 112, '乱': 113, '乲': 114, '乳': 115, '乴': 116, '乵': 117, '乶': 118, '乷': 119, '乸': 120, '乹': 121, '乺': 122, '乻': 123, '乼': 124, '乽': 125, '乾': 126, '乿': 127, '亀': 128, '亁': 129, '亂': 130, '亃': 131, '亄': 132, '亅': 133, '了': 134, '亇': 135, '予': 136, '争': 137, '亊': 138, '事': 139, '二': 140, '亍': 141, '于': 142, '亏': 143, '亐': 144, '云': 145, '互': 146, '亓': 147, '五': 148, '井': 149, '亖': 150, '亗': 151, '亘': 152, '亙': 153, '亚': 154, '些': 155, '亜': 156, '亝': 157, '亞': 158, '亟': 159, '亠': 160, '亡': 161, '亢': 162, '亣': 163, '交': 164, '亥': 165, '亦': 166, '产': 167, '亨': 168, '亩': 169, '亪': 170, '享': 171, '京': 172, '亭': 173, '亮': 174, '亯': 175, '亰': 176, '亱': 177, '亲': 178, '亳': 179, '亴': 180, '亵': 181, '亶': 182, '亷': 183, '亸': 184, '亹': 185, '人': 186, '亻': 187, '亼': 188, '亽': 189, '亾': 190, '亿': 191, '什': 192, '仁': 193, '仂': 194, '仃': 195, '仄': 196, '仅': 197, '仆': 198, '仇': 199, '仈': 200, '仉': 201, '今': 202, '介': 203, '仌': 204, '仍': 205, '从': 206, '仏': 207, '仐': 208, '仑': 209, '仒': 210, '仓': 211, '仔': 212, '仕': 213, '他': 214, '仗': 215, '付': 216, '仙': 217, '仚': 218, '仛': 219, '仜': 220, '仝': 221, '仞': 222, '仟': 223, '仠': 224, '仡': 225, '仢': 226, '代': 227, '令': 228, '以': 229, '仦': 230, '仧': 231, '仨': 232, '仩': 233, '仪': 234, '仫': 235, '们': 236, '仭': 237, '仮': 238, '仯': 239, '仰': 240, '仱': 241, '仲': 242, '仳': 243, '仴': 244, '仵': 245, '件': 246, '价': 247, '仸': 248, '仹': 249, '仺': 250, '任': 251, '仼': 252, '份': 253, '仾': 254, '仿': 255}, 1: {'伀': 0, '企': 1, '伂': 2, '伃': 3, '伄': 4, '伅': 5, '伆': 6, '伇': 7, '伈': 8, '伉': 9, '伊': 10, '伋': 11, '伌': 12, '伍': 13, '伎': 14, '伏': 15, '伐': 16, '休': 17, '伒': 18, '伓': 19, '伔': 20, '伕': 21, '伖': 22, '众': 23, '优': 24, '伙': 25, '会': 26, '伛': 27, '伜': 28, '伝': 29, '伞': 30, '伟': 31, '传': 32, '伡': 33, '伢': 34, '伣': 35, '伤': 36, '伥': 37, '伦': 38, '伧': 39, '伨': 40, '伩': 41, '伪': 42, '伫': 43, '伬': 44, '伭': 45, '伮': 46, '伯': 47, '估': 48, '伱': 49, '伲': 50, '伳': 51, '伴': 52, '伵': 53, '伶': 54, '伷': 55, '伸': 56, '伹': 57, '伺': 58, '伻': 59, '似': 60, '伽': 61, '伾': 62, '伿': 63, '佀': 64, '佁': 65, '佂': 66, '佃': 67, '佄': 68, '佅': 69, '但': 70, '佇': 71, '佈': 72, '佉': 73, '佊': 74, '佋': 75, '佌': 76, '位': 77, '低': 78, '住': 79, '佐': 80, '佑': 81, '佒': 82, '体': 83, '佔': 84, '何': 85, '佖': 86, '佗': 87, '佘': 88, '余': 89, '佚': 90, '佛': 91, '作': 92, '佝': 93, '佞': 94, '佟': 95, '你': 96, '佡': 97, '佢': 98, '佣': 99, '佤': 100, '佥': 101, '佦': 102, '佧': 103, '佨': 104, '佩': 105, '佪': 106, '佫': 107, '佬': 108, '佭': 109, '佮': 110, '佯': 111, '佰': 112, '佱': 113, '佲': 114, '佳': 115, '佴': 116, '併': 117, '佶': 118, '佷': 119, '佸': 120, '佹': 121, '佺': 122, '佻': 123, '佼': 124, '佽': 125, '佾': 126, '使': 127, '侀': 128, '侁': 129, '侂': 130, '侃': 131, '侄': 132, '侅': 133, '來': 134, '侇': 135, '侈': 136, '侉': 137, '侊': 138, '例': 139, '侌': 140, '侍': 141, '侎': 142, '侏': 143, '侐': 144, '侑': 145, '侒': 146, '侓': 147, '侔': 148, '侕': 149, '侖': 150, '侗': 151, '侘': 152, '侙': 153, '侚': 154, '供': 155, '侜': 156, '依': 157, '侞': 158, '侟': 159, '侠': 160, '価': 161, '侢': 162, '侣': 163, '侤': 164, '侥': 165, '侦': 166, '侧': 167, '侨': 168, '侩': 169, '侪': 170, '侫': 171, '侬': 172, '侭': 173, '侮': 174, '侯': 175, '侰': 176, '侱': 177, '侲': 178, '侳': 179, '侴': 180, '侵': 181, '侶': 182, '侷': 183, '侸': 184, '侹': 185, '侺': 186, '侻': 187, '侼': 188, '侽': 189, '侾': 190, '便': 191, '俀': 192, '俁': 193, '係': 194, '促': 195, '俄': 196, '俅': 197, '俆': 198, '俇': 199, '俈': 200, '俉': 201, '俊': 202, '俋': 203, '俌': 204, '俍': 205, '俎': 206, '俏': 207, '俐': 208, '俑': 209, '俒': 210, '俓': 211, '俔': 212, '俕': 213, '俖': 214, '俗': 215, '俘': 216, '俙': 217, '俚': 218, '俛': 219, '俜': 220, '保': 221, '俞': 222, '俟': 223, '俠': 224, '信': 225, '俢': 226, '俣': 227, '俤': 228, '俥': 229, '俦': 230, '俧': 231, '俨': 232, '俩': 233, '俪': 234, '俫': 235, '俬': 236, '俭': 237, '修': 238, '俯': 239, '俰': 240, '俱': 241, '俲': 242, '俳': 243, '俴': 244, '俵': 245, '俶': 246, '俷': 247, '俸': 248, '俹': 249, '俺': 250, '俻': 251, '俼': 252, '俽': 253, '俾': 254, '俿': 255}, 2: {'倀': 0, '倁': 1, '倂': 2, '倃': 3, '倄': 4, '倅': 5, '倆': 6, '倇': 7, '倈': 8, '倉': 9, '倊': 10, '個': 11, '倌': 12, '倍': 13, '倎': 14, '倏': 15, '倐': 16, '們': 17, '倒': 18, '倓': 19, '倔': 20, '倕': 21, '倖': 22, '倗': 23, '倘': 24, '候': 25, '倚': 26, '倛': 27, '倜': 28, '倝': 29, '倞': 30, '借': 31, '倠': 32, '倡': 33, '倢': 34, '倣': 35, '値': 36, '倥': 37, '倦': 38, '倧': 39, '倨': 40, '倩': 41, '倪': 42, '倫': 43, '倬': 44, '倭': 45, '倮': 46, '倯': 47, '倰': 48, '倱': 49, '倲': 50, '倳': 51, '倴': 52, '倵': 53, '倶': 54, '倷': 55, '倸': 56, '倹': 57, '债': 58, '倻': 59, '值': 60, '倽': 61, '倾': 62, '倿': 63, '偀': 64, '偁': 65, '偂': 66, '偃': 67, '偄': 68, '偅': 69, '偆': 70, '假': 71, '偈': 72, '偉': 73, '偊': 74, '偋': 75, '偌': 76, '偍': 77, '偎': 78, '偏': 79, '偐': 80, '偑': 81, '偒': 82, '偓': 83, '偔': 84, '偕': 85, '偖': 86, '偗': 87, '偘': 88, '偙': 89, '做': 90, '偛': 91, '停': 92, '偝': 93, '偞': 94, '偟': 95, '偠': 96, '偡': 97, '偢': 98, '偣': 99, '偤': 100, '健': 101, '偦': 102, '偧': 103, '偨': 104, '偩': 105, '偪': 106, '偫': 107, '偬': 108, '偭': 109, '偮': 110, '偯': 111, '偰': 112, '偱': 113, '偲': 114, '偳': 115, '側': 116, '偵': 117, '偶': 118, '偷': 119, '偸': 120, '偹': 121, '偺': 122, '偻': 123, '偼': 124, '偽': 125, '偾': 126, '偿': 127, '傀': 128, '傁': 129, '傂': 130, '傃': 131, '傄': 132, '傅': 133, '傆': 134, '傇': 135, '傈': 136, '傉': 137, '傊': 138, '傋': 139, '傌': 140, '傍': 141, '傎': 142, '傏': 143, '傐': 144, '傑': 145, '傒': 146, '傓': 147, '傔': 148, '傕': 149, '傖': 150, '傗': 151, '傘': 152, '備': 153, '傚': 154, '傛': 155, '傜': 156, '傝': 157, '傞': 158, '傟': 159, '傠': 160, '傡': 161, '傢': 162, '傣': 163, '傤': 164, '傥': 165, '傦': 166, '傧': 167, '储': 168, '傩': 169, '傪': 170, '傫': 171, '催': 172, '傭': 173, '傮': 174, '傯': 175, '傰': 176, '傱': 177, '傲': 178, '傳': 179, '傴': 180, '債': 181, '傶': 182, '傷': 183, '傸': 184, '傹': 185, '傺': 186, '傻': 187, '傼': 188, '傽': 189, '傾': 190, '傿': 191, '僀': 192, '僁': 193, '僂': 194, '僃': 195, '僄': 196, '僅': 197, '僆': 198, '僇': 199, '僈': 200, '僉': 201, '僊': 202, '僋': 203, '僌': 204, '働': 205, '僎': 206, '像': 207, '僐': 208, '僑': 209, '僒': 210, '僓': 211, '僔': 212, '僕': 213, '僖': 214, '僗': 215, '僘': 216, '僙': 217, '僚': 218, '僛': 219, '僜': 220, '僝': 221, '僞': 222, '僟': 223, '僠': 224, '僡': 225, '僢': 226, '僣': 227, '僤': 228, '僥': 229, '僦': 230, '僧': 231, '僨': 232, '僩': 233, '僪': 234, '僫': 235, '僬': 236, '僭': 237, '僮': 238, '僯': 239, '僰': 240, '僱': 241, '僲': 242, '僳': 243, '僴': 244, '僵': 245, '僶': 246, '僷': 247, '僸': 248, '價': 249, '僺': 250, '僻': 251, '僼': 252, '僽': 253, '僾': 254, '僿': 255}, 3: {'儀': 0, '儁': 1, '儂': 2, '儃': 3, '億': 4, '儅': 5, '儆': 6, '儇': 7, '儈': 8, '儉': 9, '儊': 10, '儋': 11, '儌': 12, '儍': 13, '儎': 14, '儏': 15, '儐': 16, '儑': 17, '儒': 18, '儓': 19, '儔': 20, '儕': 21, '儖': 22, '儗': 23, '儘': 24, '儙': 25, '儚': 26, '儛': 27, '儜': 28, '儝': 29, '儞': 30, '償': 31, '儠': 32, '儡': 33, '儢': 34, '儣': 35, '儤': 36, '儥': 37, '儦': 38, '儧': 39, '儨': 40, '儩': 41, '優': 42, '儫': 43, '儬': 44, '儭': 45, '儮': 46, '儯': 47, '儰': 48, '儱': 49, '儲': 50, '儳': 51, '儴': 52, '儵': 53, '儶': 54, '儷': 55, '儸': 56, '儹': 57, '儺': 58, '儻': 59, '儼': 60, '儽': 61, '儾': 62, '儿': 63, '兀': 64, '允': 65, '兂': 66, '元': 67, '兄': 68, '充': 69, '兆': 70, '兇': 71, '先': 72, '光': 73, '兊': 74, '克': 75, '兌': 76, '免': 77, '兎': 78, '兏': 79, '児': 80, '兑': 81, '兒': 82, '兓': 83, '兔': 84, '兕': 85, '兖': 86, '兗': 87, '兘': 88, '兙': 89, '党': 90, '兛': 91, '兜': 92, '兝': 93, '兞': 94, '兟': 95, '兠': 96, '兡': 97, '兢': 98, '兣': 99, '兤': 100, '入': 101, '兦': 102, '內': 103, '全': 104, '兩': 105, '兪': 106, '八': 107, '公': 108, '六': 109, '兮': 110, '兯': 111, '兰': 112, '共': 113, '兲': 114, '关': 115, '兴': 116, '兵': 117, '其': 118, '具': 119, '典': 120, '兹': 121, '兺': 122, '养': 123, '兼': 124, '兽': 125, '兾': 126, '兿': 127, '冀': 128, '冁': 129, '冂': 130, '冃': 131, '冄': 132, '内': 133, '円': 134, '冇': 135, '冈': 136, '冉': 137, '冊': 138, '冋': 139, '册': 140, '再': 141, '冎': 142, '冏': 143, '冐': 144, '冑': 145, '冒': 146, '冓': 147, '冔': 148, '冕': 149, '冖': 150, '冗': 151, '冘': 152, '写': 153, '冚': 154, '军': 155, '农': 156, '冝': 157, '冞': 158, '冟': 159, '冠': 160, '冡': 161, '冢': 162, '冣': 163, '冤': 164, '冥': 165, '冦': 166, '冧': 167, '冨': 168, '冩': 169, '冪': 170, '冫': 171, '冬': 172, '冭': 173, '冮': 174, '冯': 175, '冰': 176, '冱': 177, '冲': 178, '决': 179, '冴': 180, '况': 181, '冶': 182, '冷': 183, '冸': 184, '冹': 185, '冺': 186, '冻': 187, '冼': 188, '冽': 189, '冾': 190, '冿': 191, '净': 192, '凁': 193, '凂': 194, '凃': 195, '凄': 196, '凅': 197, '准': 198, '凇': 199, '凈': 200, '凉': 201, '凊': 202, '凋': 203, '凌': 204, '凍': 205, '凎': 206, '减': 207, '凐': 208, '凑': 209, '凒': 210, '凓': 211, '凔': 212, '凕': 213, '凖': 214, '凗': 215, '凘': 216, '凙': 217, '凚': 218, '凛': 219, '凜': 220, '凝': 221, '凞': 222, '凟': 223, '几': 224, '凡': 225, '凢': 226, '凣': 227, '凤': 228, '凥': 229, '処': 230, '凧': 231, '凨': 232, '凩': 233, '凪': 234, '凫': 235, '凬': 236, '凭': 237, '凮': 238, '凯': 239, '凰': 240, '凱': 241, '凲': 242, '凳': 243, '凴': 244, '凵': 245, '凶': 246, '凷': 247, '凸': 248, '凹': 249, '出': 250, '击': 251, '凼': 252, '函': 253, '凾': 254, '凿': 255}}
        self.mode = mode
        self.characterDictionary = self.genDictionary()
        self.valueDictionary = self.genDictionary(True)
        self.image_path = image_path
        if len(imageSize) < 2:
            self.width = Image.open(self.image_path).width
            self.height = Image.open(self.image_path).height
        else:
            self.width = imageSize[0]
            self.height = imageSize[1]
        self.pixel_values = self.roundImage(self.get_pixel_values(),round)

        if encoded_char_set == "":
            self.encoded_char_set = self.rle_encode(self.split_char_sets_groups(self.every_pixel_convert_to_charset_string()))
        else:
            self.encoded_char_set = encoded_char_set
    
    def genDictionary(self, reverse=False):
        dictionary = {}

        for block in range(len(self.mode)):
            sub_dict = {}
            base = 19968 + block * 256
            for i in range(256):
                sub_dict[i] = chr(base + i)
            dictionary[block] = sub_dict  # Store each block as a sub-dictionary
        if reverse:
            for diction in range(len(dictionary.values())):
                keyValue = list(dictionary.keys())[diction]
                dictionary[keyValue] = {value: key for key, value in  dictionary[keyValue].items()}
        return dictionary
    
    def roundImage(self, pixelValues, roundAmount):
        if roundAmount > 0:
            imageList = pixelValues
            for pixelIndex in range(len(imageList)):
                createdPixel = []
                for val in range(len(imageList[pixelIndex])):
                    createdPixel.append(min((int(round(imageList[pixelIndex][val]/roundAmount))*roundAmount),255))
                imageList[pixelIndex] = tuple(createdPixel)
            return imageList
        else:
            return pixelValues

    def __str__(self):
        return self.encoded_char_set

    def get_pixel_values(self):
        try:
            im = Image.open(self.image_path).convert(self.mode)
            return list(im.getdata())
        except FileNotFoundError:
            raise FileNotFoundError(f"The file '{self.image_path}' was not found.")
        except Exception as e:
            raise RuntimeError(f"Error opening or processing the image: {e}")

    def convert_pixel_to_character_set(self, pixel):
        output_character_set = ""
        for i in range(len(pixel)):
            output_character_set += self.characterDictionary[i][pixel[i]]
        return output_character_set

    def convert_character_set_to_pixel(self, char_set):
        pixel = []
        for i in range(len(char_set)):
            pixel.append(self.valueDictionary[i][char_set[i]])
        return tuple(pixel)

    def every_pixel_convert_to_charset_string(self):
        char_sets = ""
        pixel_values = self.pixel_values
        for pixel in pixel_values:
            char_sets += self.convert_pixel_to_character_set(pixel)
        return char_sets

    def every_charset_to_pixels(self, char_sets):
        pixel_values = []
        for group in self.split_char_sets_groups(char_sets):
            pixel_values.append(self.convert_character_set_to_pixel(group))
        return pixel_values

    def split_char_sets_groups(self, char_set_string):
        #r'(.{1,4})'
        regex = r'(.{1,'+re.escape(f"{len(self.mode)}")+r'})'
        return re.findall(regex, char_set_string)

    def rle_encode(self,arr):
        result = []

        for i in range(len(arr)):
            current = arr[i]
            if not result or result[-1][0] != current:
                #adds new value
                result.append([current, 1])
            else:
                #increases count
                result[-1][1] += 1

        return ''.join([f"{count}{item}" if count > 1 else f"{item}" for item, count in result])

    def rle_decode(self, encoded_str):
        #finds pairs of numbers and 4 non number characters
        #r'(\d+)?([^\d]{4})'
        regex = r'(\d+)?([^\d]{' + re.escape(f"{len(self.mode)}")+ '})'
        stringNumberPairs = [(int(match[0]) if match[0] != '' else 1, match[1]) for match in re.findall(regex, encoded_str)]
        #splits numbers and characer sets
        numbers = [item[0] for item in stringNumberPairs]
        strings = [item[1] for item in stringNumberPairs]
        decoded_list = []

        #increases string length to contain original character sets
        for num, string in zip(numbers, strings):
            decoded_list.extend([string] * num)
        return decoded_list

    def decode(self):
        #combines methods to properly reconstruct image pixels
        decoded_char_set = ''.join(self.rle_decode(self.encoded_char_set))
        reconstructed_pixels = self.every_charset_to_pixels(decoded_char_set)
        return reconstructed_pixels

    def getImage(self):
        new_image = Image.new(self.mode, (self.width, self.height))
        new_image.putdata(self.decode())
        return new_image

def select_file():
    root = tk.Tk()
    root.withdraw()  # Hide the root window
    file_path = filedialog.askopenfilename(
        title="Select an Image File",
        filetypes=[("Image files", "*.jpg;*.jpeg;*.png;*.bmp;*.gif")]
    )  # Open file dialog to select an image file
    return file_path

'''
if __name__ == "__main__":
    image_path = 'PythonTesting/test.png'
    output_path = 'PythonTesting/output.png'

    processor = ImageProcessor(image_path, round=10, mode='RGBA')
    #print(processor)
    processor.getImage().save(output_path)
'''