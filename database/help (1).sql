-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2024 at 08:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `help`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_station`
--

CREATE TABLE `tbl_station` (
  `id` int(11) NOT NULL,
  `station_id` varchar(255) NOT NULL,
  `station_name` varchar(255) DEFAULT NULL,
  `station_type` varchar(255) DEFAULT NULL,
  `province` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_station`
--

INSERT INTO `tbl_station` (`id`, `station_id`, `station_name`, `station_type`, `province`) VALUES
(253, 'F601', 'F601 Station Neak Vorn', 'COCO', 'Phnom Penh'),
(254, 'F603', 'F603 Station Klaing Leu', 'COCO', 'Preah Sihanouk'),
(255, 'F604', 'F604 Station Seang Nam', 'COCO', 'Siem Reap'),
(256, 'F605', 'F605 Station Chba Ampov', 'COCO', 'Phnom Penh'),
(257, 'F606', 'F606 Staton Siem Reap', 'COCO', 'Siem Reap'),
(258, 'F608', 'F608 Station Phdao Chum I', 'COCO', 'Kampong Cham'),
(259, 'F609', 'F609 Station Pich Nil', 'COCO', 'Kampong Speu'),
(260, 'F610', 'F610 Station Prey Key', 'COCO', 'Phnom Penh'),
(261, 'F611', 'F611 Station Takeo City', 'COCO', 'Takeo'),
(262, 'F612', 'F612 Station Kampong Chhnang', 'COCO', 'Kampong Chhnang'),
(263, 'F613', 'F613 Station Prek Phnov', 'COCO', 'Phnom Penh'),
(264, 'F614', 'F614 Station Phdao-Chum II', 'COCO', 'Kampong Cham'),
(265, 'F617', 'F617 PTT Prey Sar', 'COCO', 'Phnom Penh'),
(266, 'F615', 'F615 PTT Veng Sreng', 'COCO', 'Phnom Penh'),
(267, 'F618', 'F618 Station Phdao-Chum lll', 'COCO', 'Kampong Cham'),
(268, '10029691', 'PTT Aeon 2_KHOU FAMILY PETROL STATION_10029691', 'DODO', 'Phnom Penh'),
(269, '10032997', 'PTT AKREIY KSAT_CHAE HIENG AKREIY KS_10032997', 'DODO', 'Kandal'),
(270, '10033524', 'PTT-Station-Grand Phnom Penh II', 'DODO', 'Phnom Penh'),
(271, '10032217', 'PTT Station ChroyChangVa 3_CHAMRAON CHROYCHANGVA_10032217', 'DODO', 'Phnom Penh'),
(272, '10029692', 'PTT Prek Tameak_RVN Siblings Co.,LTD_10029692', 'DODO', 'Kandal'),
(273, '10032352', 'PTT Khmounh Sen Sok_CSR-KPN INVESTMENT CO.,LTD_10032352', 'DODO', 'Phnom Penh'),
(274, '10032353', 'PTT Moil Villlage_LK CENTRAL INVESTMEN_ 10032353', 'DODO', 'Phnom Penh'),
(275, '10033523', 'PTT DEY THMEY_KONGKEA DEY THMEY_10033523', 'DODO', 'Phnom Penh'),
(276, '10033316', 'PTT VITHOURAK_SOK KEO KOUR SROV_10033316', 'DODO', 'Phnom Penh'),
(277, '10033821', 'PTT Road 61_SRUN VATTANAC (BY SRUN) CO., LTD_10033821', 'DODO', 'Kampong Cham'),
(278, '10030896', 'PTT OUDONG_VAN SV TRADING CO.,LTD_10030896', 'DODO', 'Kampong Chhnang'),
(279, '10032893', 'PTT Wat Tul_MULAKA CHHEANGLAI_10032893 (TV Town)', 'DODO', 'Phnom Penh'),
(280, '10032705', 'PTT DOM DEK_NALENG KMP CO.,LTD_10032705', 'DODO', 'Siem Reap'),
(281, '10031114', 'PTT KOMPONGTHOM 2_ KETYA RITHIYA CO.,LTD_ 10031114', 'DODO', 'Kampong Thom'),
(282, '10030505', 'PTT BTB Before City_CHHIV LY CHHENG INVESTMENT CO.,LT_10030505', 'DODO', 'Battambang'),
(283, '10031826', 'PTT Battambang Road57_K F H I CO.,LTD_10031826', 'DODO', 'Battambang'),
(284, '10027087', 'PTT KOMPOT I_ RADITA CO., LTD_ 10027087', 'DODO', 'Kampot'),
(285, '10026213', 'PTT Mony Oudom(N21)_MONYOUDOM PTT Petrol_10026213', 'DODO', 'Kandal'),
(286, '10027210', 'PTT SOK KEO PETROL STATION_ 10027210', 'DODO', 'Takeo'),
(287, '10025514', 'PTT Pasteur_ MC LAKSMI INVESTMENT_ 10025514', 'DODO', 'Phnom Penh'),
(288, '10034019', 'PTT_PREK ANCHANH_SENGNETH CO.,LTD_10034019', 'DODO', 'Phnom Penh'),
(289, '10033146', 'PTT THMOR KOL I_SATHANYBRENG ENTHANAK KIM HENG_10033146', 'DODO', 'Battambang'),
(290, '10027790', 'PTT ChomkaDoung_ S.J.K PETROL STATION_ 10027790', 'DODO', 'Phnom Penh'),
(291, '10029893', 'PTT MEETING POINT(CHAN KRY)_10029893', 'DODO', 'Phnom Penh'),
(292, '10030924', 'PTT KS TOUL PONGRO STATION_10030924', 'DODO', 'Phnom Penh'),
(293, '10030247', 'PTT Prey Sar_MOM2K Investment Co., LTD_10030247', 'DODO', 'Phnom Penh'),
(294, '10030894', 'PTT KANDAL STEUNG_ TALOEK PETROL STATION_ 10030894', 'DODO', 'Kandal'),
(295, '10034050', 'PTT KHORK KHLEANG GAS STATION_ 10034050', 'DODO', 'Phnom Penh'),
(296, '10026251', 'PTT Station Airport_ ADVANCED BROTHERS_ 10026251', 'DODO', 'Phnom Penh'),
(297, '10032197', 'PTT Staion TK _DARA RITH OIL IMPORT EXPORT_10032197', 'DODO', 'Phnom Penh'),
(298, '10025835', 'PTT Maeda(New)_ LKMG ONE CAPITAL INV_10025835', 'DODO', 'Phnom Penh'),
(299, '10027083', 'PTT Central Market_ CENTRAL BROTHERS CO_ 10027083', 'DODO', 'Phnom Penh'),
(300, '10027423', 'PTT 371 BROTHERS CO., LTD_ 10027423', 'DODO', 'Phnom Penh'),
(301, '10027424', 'PTT St 430_ H.V BUSINESS CO., LTD_ 10027424', 'DODO', 'Phnom Penh'),
(302, '10027728', 'PTT TEP PORN_ T-BROTHERS (CAMBODIA_ 10027728', 'DODO', 'Phnom Penh'),
(303, '10023722', 'PTT Station Mongkol_ 10023722', 'DODO', 'Phnom Penh'),
(304, '10026809', 'PTT Changr? Krom_ NANA VIKA_10026809', 'DODO', 'Phnom Penh'),
(305, '10029690', 'PTT Chaom Chao_SONITA SD PETROLEUM_10029690', 'DODO', 'Phnom Penh'),
(306, '10027224', 'PTT Kok Klang/Beung Chhouk_CHHENG HUYTEANG TRAD_ 10027224', 'DODO', 'Phnom Penh'),
(307, '10026802', 'PTT CHROYCHANGVA I_ SC&HH CO., LTD_ 10026802', 'DODO', 'Phnom Penh'),
(308, '10028079', 'PTT THIGER Factory Road_  SOK SAN PN168 CO., L_10028079', 'DODO', 'Phnom Penh'),
(309, '10034689', 'PTT Reusey Sanh _ DEN RUSSEY _ 10034689', 'DODO', 'Phnom Penh'),
(310, '10034999', 'PTT Station Hanoi BLVD_NRSV STATION_10034999', 'DODO', 'Phnom Penh'),
(311, '10034862', 'PTT Tuol Tom Pong _ ROS RATHA TOUL TOM POUNG _ 10034862 ', 'DODO', 'Phnom Penh'),
(312, '10034074', 'PTT Prek Ho _RTNK INVESTMENT_ 10034074', 'DODO', 'Kandal'),
(313, '10034461', 'PTT Boeng Thom_VFAMILY SHOP_10034461', 'DODO', 'Phnom Penh'),
(314, '10023796', 'PTT Toul Sangke_ LAM SOPHEAK_ 10023796', 'DODO', 'Phnom Penh'),
(315, '10032855', 'PTT_MAO SEAKHONG GRAND PHNOM PENH PETROL STATION_10032855', 'DODO', 'Phnom Penh'),
(316, '10023427', 'PTT Kbal Tnal', 'DODO', 'Phnom Penh'),
(317, '10031806', 'PTT Maeda (Old)', 'DODO', 'Phnom Penh'),
(318, '10021637', 'SAN BOKOR PETROL STATION_PTT Bokor', 'DODO', 'Phnom Penh'),
(319, '10021912', 'PTT Ta Khmao', 'DODO', 'Kandal'),
(320, '10021709', 'PTT Tonle Oum I', 'DODO', 'Kampong Cham'),
(321, '10021714', 'PTT Lim Hay Heng (O\'Char)', 'DODO', 'Battambang'),
(322, '10021644', 'PTT Bun Sary I (Svay Dong Kom) ', 'DODO', 'Siem Reap'),
(323, '10025909', 'PTT Bun Sary II_SARY BUSINESS POWER_10025909', 'DODO', 'Siem Reap'),
(324, '10021643', '10021643_PTT TADAMBANGKRORNHUONG', 'DODO', 'Battambang'),
(325, '10021648', 'PTT Camko City_  KOY CHHENG TOULSANGK_ 10021648', 'DODO', 'Phnom Penh'),
(326, '10023212', 'PTT Hanoi (C-7)_  PPT C7_ 10023212', 'DODO', 'Phnom Penh'),
(327, '10023463', 'PTT Kratie_ CHAN SOTHY PETROL STATION_ 10023463', 'DODO', 'Kratie'),
(328, '10030619', 'PTT NORTH BRIDGE_NBSS PETROL STATION_10030619', 'DODO', 'Phnom Penh'),
(329, '10023997', 'PTT Poi Pet 1_ TAING KUYHAK_ 10023997', 'DODO', 'Banteay Meanchey'),
(330, '10025350', 'PTT INTRADEVY Petrol_ 10025350', 'DODO', 'Phnom Penh'),
(331, '30063513', 'PTT Angten Toul Kork_ BRAND OF PTT INTRADE_ 30063513', 'DODO', 'Phnom Penh'),
(332, '10025853', 'PTT Beoung Trabek_  MOMGP PETROL STATION_10025853', 'DODO', 'Phnom Penh'),
(333, '10025918', 'PTT Kampong Thom_ EANG MENG_10025918', 'DODO', 'Kampong Thom'),
(334, '10026283', 'PTT Steuong Mean Chey_ S S M C_ 10026283', 'DODO', 'Phnom Penh'),
(335, '10030392', 'PTT Phnom Penh Thmey', 'DODO', 'Phnom Penh'),
(336, '10026452', 'PTT ROLANG SANGKAE PETRO_ 10026452', 'DODO', 'Kampong Speu'),
(337, '10026578', 'PTT Mong Rithy_V.LAN INVESTMENT CO_10026578', 'DODO', 'Phnom Penh'),
(338, '10026673', 'PTT SHIHANOUK VILE (LNTN)_LNTN CO., LTD_ 10026673', 'DODO', 'Preah Sihanouk'),
(339, '10026852', 'PTT Tram Khnar_ FIR PLANNED CO., LTD_10026852', 'DODO', 'Kampong Speu'),
(340, '10031916', 'PTT  St.271 I (Mrs. Seak Lyheang)', 'DODO', 'Phnom Penh'),
(341, '30057218', 'PTT WAT PHNOM_ BRANCH OF MC LAKSMI_30057218', 'DODO', 'Phnom Penh'),
(342, '10021647', 'PTT Tonle-Aum II_ KIM SENG FUEL STATION_ 10021647', 'DODO', 'Kampong Cham'),
(343, '10027716', 'PTT St.H.E Chea Sophara_ C.K.M.C CO., LTD_10027716', 'DODO', 'Phnom Penh'),
(344, '10030779', 'PTT BEKCHAN (K.Speu)', 'DODO', 'Kandal'),
(345, '10027422', 'PTT NAK LEUNG_NGY HEANG TRADING CO_ 10027422', 'DODO', 'Prey Veng'),
(346, '10032579', 'PTT OLYMPIC_VL OLYMPIC PETROL STATION_10032579', 'DODO', 'Phnom Penh'),
(347, '10028453', 'PTT Rd.Samdech Chearsim_CHHUN KIMLONG PETROL_10028453', 'DODO', 'Phnom Penh'),
(348, '10028690', 'PTT SAMROS PREY PRING Petrol Station_ 10028690', 'DODO', 'Phnom Penh'),
(349, '10028834', 'PTT CT_ VIMEAN MORODOK ANGKO_10028834', 'DODO', 'Preah Sihanouk'),
(350, '10028725', 'PTT KIEMNY RATANAKIRI_ 10028725', 'DODO', 'Ratanakiri'),
(351, '10028650', 'PTT K.Speu City_DEPOT CHHUN CHHIN_10028650', 'DODO', 'Kampong Speu'),
(352, '10028689', 'PTT ChroyChongVa II_R6 BROTHERS_10028689', 'DODO', 'Phnom Penh'),
(353, '10028776', 'PTT OBEK KHAOM PETROL STATION(St.271)_ 10028776', 'DODO', 'Phnom Penh'),
(354, '10028779', 'PTT Steung Treng _CAMKO MART CHAIN STORE_10028779', 'DODO', 'Stung Treng'),
(355, '10029265', 'PTT Poi Pet(Out City)_PTT STUENG BAT_10029265', 'DODO', 'Banteay Meanchey'),
(356, '10029001', 'PTT PREK ENG_NYSA PHLOV CHEAT LEK 1 PETROL STATION _ 10029001', 'DODO', 'Phnom Penh'),
(357, '10028452', 'PTT Young Youth Co., LTD (Rd No.173)_10028452', 'DODO', 'Phnom Penh'),
(358, '10029430', 'PTT Station Road 41_ THNAL TOTUENG PETROL_10029430', 'DODO', 'Kampong Speu'),
(359, '10029632', 'PTT Phnom Prasit_KRUDTEP PROSITH CO.,LTD_ 10029632', 'DODO', 'Phnom Penh'),
(360, '10029925', 'PTT Road Kob Srov_ALX Enterprise Co. LTD_10029925', 'DODO', 'Phnom Penh'),
(361, '10030168', 'PTT Vimean Chhnas Chhnas_PREK TA SEK PETROL S_10030168', 'DODO', 'Phnom Penh'),
(362, '10030169', 'PTT Krang Thnoung_V.LAN STATION CO.,LTD_10030169', 'DODO', 'Phnom Penh'),
(363, '10030440', 'PTT Boun Sary 3_TC PETROLEUM CO.,LTD_ 10030440', 'DODO', 'Siem Reap'),
(364, '10030456', 'PTT Kampot City_ KAMPOT RADITA DEVELOPMENT_10030456', 'DODO', 'Kampot'),
(365, '10030170', 'PTT Rd.Win Win BLVD(Rebrand Sokimex)_GHB INVESTMENT CO.,LTD_ 10030170', 'DODO', 'Phnom Penh'),
(366, '10029894', 'PTT Kbal Domrey_ SISTERS ENERGY & DEV_10029894', 'DODO', 'Phnom Penh'),
(367, '10029786', 'PTT BANTEAY MEANCHEY_10029786', 'DODO', 'Banteay Meanchey'),
(368, '10030375', 'PTT SVAY RIENG_H.MALY PETROL STATION_10030375', 'DODO', 'Svay Rieng'),
(369, '10030951', 'PTT STEUNG MEANCHEY_VORN SEARLENG CO.,LTD_ 10030951', 'DODO', 'Phnom Penh'),
(370, '10030973', 'PTT PURSAT I_ SO KEAT CO.,LTD_10030973', 'DODO', 'Pursat'),
(371, '10030895', 'PTT SVAY PAK_ SAFE TRIP GAS STATION_10030895', 'DODO', 'Phnom Penh'),
(372, '10030643', 'PTT OU KREANG_ CHEA SUYLY PETROL ST_ 10030643', 'DODO', 'Kratie'),
(373, '10031131', 'PTT OU REUSSEY_PHU CHHAI KEANG CO.,_ 10031131', 'DODO', 'Phnom Penh'),
(374, '10031323', 'PTT REAM SHV_ DANH PHALY TRADING C_ 10031323', 'DODO', 'Preah Sihanouk'),
(375, '30068426', 'PTT ANG TASOM_ Branch of MOM 2DK IN_30068426', 'DODO', 'Takeo'),
(376, '10031787', 'PTT O3_ HENG KIMCHY INVESTME_ 10031787', 'DODO', 'Preah Sihanouk'),
(377, '10031481', 'PTT AU CHAR 2_ AU CHAR PETROL STATION_  10031481', 'DODO', 'Battambang'),
(378, '10031773', 'PTT MOLYKA KRANG THOUNG_ 10031773', 'DODO', 'Phnom Penh'),
(379, '10031788', 'PTT Slap Laeng Road 41_ NKSCJ INVESTMENT CO_ 10031788', 'DODO', 'Kampong Speu'),
(380, '10031990', 'PTT Tunle Bati_MOM SOMANY CO.,LTD_ 10031990', 'DODO', 'Takeo'),
(381, '10032354', 'PTT road 51 right hand_LSLCN TRADING Co.,LTD_10032354', 'DODO', 'Kandal'),
(382, '10032399', 'PTT Bavit_HOK LAYHORN PETROL S_10032399', 'DODO', 'Svay Rieng'),
(383, '10032587', 'PTT KHSACH KANDAL_SATHANY PRENG ENTHENEAK VENG LENG ENG KHSACH KANDAL_10032587', 'DODO', 'Kandal'),
(384, '10032306', 'PTT SOUNG_HONG SOTHEA TRANDING_10032306', 'DODO', 'Tboung Khmum'),
(385, '10032708', 'PTT  KOH KRABEI PETROL STATION_10032708', 'DODO', 'Phnom Penh'),
(386, '10032704', 'PTT DOM DEK II_SARY CHINDA INVESTME_10032704', 'DODO', 'Siem Reap'),
(387, '10032943', 'PTT Veal Renh_Jenny Investment_10032943', 'DODO', 'Preah Sihanouk'),
(388, '10032355', 'PTT_Road 51_WE RISE CO., LTD_10032355', 'DODO', 'Kampong Speu'),
(389, '10032944', 'PTT Asian Hope_L.H.M.L PETROL STATION_10032944', 'DODO', 'Phnom Penh'),
(390, '10032709', 'PTT Road 6A Lyong Phat Bridge_SONYA NIRUN_10032709', 'DODO', 'Phnom Penh'),
(391, '10032710', 'PTT Tropeng Andek_Bright Wills Co., Ltd_10032710 ', 'DODO', 'Takeo'),
(392, '30076521', 'PTT Dorng Tung _  ANGKOR PROTOTYPE LTD _ 30076521', 'DODO', 'Koh Kong'),
(393, '10033986', 'PTT Phsar KorKi _ KHEMACOCO CO.,LTD _ 10033986', 'DODO', 'Kandal'),
(394, '10034008', 'PTT Kro Kor _ W.H.Y INVESTMENT _ 10034008', 'DODO', 'Pursat'),
(395, '10034077', 'PTT Niroth _ PICH PANCHAKPOR TRAD _ 10034077', 'DODO', 'Phnom Penh'),
(396, '10033884', 'PTT KAMPOT OUT CITY_ Baoli Investment Co.,Ltd _ 10033884', 'DODO', 'Kampot'),
(397, '10034109', 'PTT Domnak Chanour_ HENG TITO PETROLEUM_ 10034109', 'DODO', 'Kep'),
(398, '10034007', 'PTT Sree 100 _ LIM OU INVESTMENT_ 10034007', 'DODO', 'Battambang'),
(399, '10033836', 'PTT_Snoul_Gem Gemy Reachny_10033836', 'DODO', 'Kratie'),
(400, '10034915', 'PTT Kampot Borkor_FNS BOKOR_10034915', 'DODO', 'Kampot'),
(401, '30077267', 'PTT Mongkol Borie_BRANCH OF TC PETROLE_30077267', 'DODO', 'Banteay Meanchey'),
(402, '10034962', 'PTT Kompong Thmor_PLTH INVESTMENT_10034962', 'DODO', 'Kampong Thom'),
(403, '10043893', 'PTT Krung Prey Veng_SOKHEY KRONG PREY VENG_10034893', 'DODO', 'Prey Veng'),
(404, '10035075', 'PTT Toul Svay Prey_SOVATANAK PIPHOU_10035075', 'DODO', 'Phnom Penh'),
(405, '10035074', 'PTT Chhuk Commune_Ratanak Chhouk_10035074', 'DODO', 'Kampot'),
(406, '10035079', 'PTT Staion Road 105K_VS NAKAH_10035079', 'DODO', 'Phnom Penh'),
(407, '10034985', 'PTT Tmor Koul II_ UNG YOUPENG_10034985', 'DODO', 'Battambang'),
(408, '10034963', 'PTT Svay Autor_TARGET POINT_10034963', 'DODO', 'Prey Veng'),
(409, '30078128', 'PTT Loung Mea_MOM 2DK INVESTMENT_30078128 ', 'DODO', 'Phnom Penh'),
(410, '10035118', 'PTT O\'Tress _ JULIE COOPERATION _ 10035118', 'DODO', 'Preah Sihanouk'),
(411, '10034777', 'PTT Chi Phou _  KTCCL INVESTMENT _ 10034777 ', 'DODO', 'Svay Rieng'),
(412, '30075518', 'PTT Battambong City _ Branch of SATHANYBRENG ENTHANAK KIM HENG _ 30075518 ', 'DODO', 'Battambang'),
(413, '10035275', 'PTT BANTEAY MEANCHEY in CITY - RL CENTRAL - 10035275', 'DODO', 'Banteay Meanchey'),
(414, '10035465', 'PTT Moung Russey_S T VATHANAK_10035465', 'DODO', 'Battambang'),
(415, '10035495', 'PTT Vong Plov 20A_VONG PLOV 20A_10035495', 'DODO', 'Phnom Penh'),
(416, '10035664', 'PTT Sambour Meas_LHK PETROL STATION_10035664', 'DODO', 'Phnom Penh'),
(417, '10035854', 'PTT Kakap ( 123 KAKAP PETROL STATION) 0010035854', 'DODO', 'Phnom Penh'),
(418, '10035000', 'PTT_ANG SNUOL_SPSK INVESTMENT_10035000', 'DODO', 'Kandal'),
(419, '10035868', 'PTT_PONGRO NR5 PETROL STATION_10035868', 'DODO', 'Kampong Chhnang'),
(420, '30080329', 'PTT_Boeung Tamok CSR-KPN INVESTMENT CO.,LTD._0030080329 ', 'DODO', 'Phnom Penh'),
(421, '10038024', 'PTT Peng Huoth The Grand Star Platinum (THAY ENERGY CO., LTD.) 0010038024', 'DODO', 'Phnom Penh'),
(422, '10035930', 'PTT_PonheaKrek_PICH HENG DARA INVESTMENT Co.,Ltd_10035930', 'DODO', 'Tboung Khmum'),
(423, '10035641', 'PTT_KAMPONG SEILA_ANGEL VOROYOS_10035641', 'DODO', 'Preah Sihanouk'),
(424, '10036179', 'PTT_Kamchay Mea_CT SIV Co.,Ltd_10036179', 'DODO', 'Prey Veng'),
(425, '30082047', 'PTT KOH NOREA (LOCAL BRANCH OF MC LAKSMI INVESTMENT CO., LTD.) 30082047', 'DODO', 'Phnom Penh'),
(426, '10037095', 'PTT_Borey Keila_RITA FAMILY_10037095', 'DODO', 'Phnom Penh'),
(427, '10037152', 'PTT Station Kobsrob Boeung Tamok-GODSRISE CO., LTD._0010037152 ', 'DODO', 'Phnom Penh'),
(428, '30082667', 'PTT Peng Huoth Road 50m (LOCAL BRANCH OF MOM 2DK INVESTMENTC O., LTD.) 10030247 ', 'DODO', 'Phnom Penh'),
(429, '10037284', 'PTT Pursat II ( SK KIMCHENG CO.,LTD) ', 'DODO', 'Pursat'),
(430, '10037327', 'PTT_Veng Sreng Center_KIMCHHONG ENTERPRISE_10037327', 'DODO', 'Phnom Penh'),
(431, '30083692', 'PTT Poi Pet Five Star_SATHANYBRENG ENTHANAK STUENG BAT_30083692', 'DODO', 'Banteay Meanchey'),
(432, '10037634', 'PTT S\'ang Tuol Krasang (LY KONG LK INVESTMENT CO., LTD.)_0010037634', 'DODO', 'Kandal'),
(433, '10037836', 'PTT Kralanh ( 3S PETROLEUM CO., LTD)', 'DODO', 'Siem Reap'),
(434, '10037871', 'PTT Kob Srov Golf Club (O.S.E.L.K.H TRADING CO., LTD.)', 'DODO', 'Phnom Penh');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ticket`
--

CREATE TABLE `tbl_ticket` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `station_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `station_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `station_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `issue_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `SLA_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `users_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ticket_open` datetime DEFAULT NULL,
  `ticket_on_hold` datetime DEFAULT NULL,
  `ticket_in_progress` datetime DEFAULT NULL,
  `ticket_pending_vendor` datetime DEFAULT NULL,
  `ticket_close` datetime DEFAULT NULL,
  `ticket_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_create_ticket` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ticket_images`
--

CREATE TABLE `tbl_ticket_images` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ticket_track`
--

CREATE TABLE `tbl_ticket_track` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(250) NOT NULL,
  `open_time` datetime NOT NULL,
  `modify_time` datetime DEFAULT NULL,
  `modified_by` int(11) NOT NULL,
  `issue_type` text DEFAULT NULL,
  `SLA_category` text DEFAULT NULL,
  `assign` text DEFAULT NULL,
  `status` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `users_id` int(11) NOT NULL,
  `users_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` mediumint(50) NOT NULL COMMENT '0 = verified ',
  `status` tinyint(1) NOT NULL COMMENT '0 = inactive ,1 = active',
  `rules_id` int(11) DEFAULT NULL,
  `company` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`users_id`, `users_name`, `email`, `password`, `code`, `status`, `rules_id`, `company`) VALUES
(37, 'admin', 'nun@ptt.com', '$2y$10$9e6KqmpVXo.eqHRSA1QveOW78cvc2YUpOtNnl/W6wh1liHkCto2gO', 0, 1, 1461, 'PTTCL'),
(55, 'Chhoy Too', 'too.ch@ptt.com.kh', '$2y$10$cM1YiSxTsHiW0zYXjTO3LuQfKBfGY3KD2WjNPHwjn0MktMrqG2/Ea', 0, 1, 1472, 'ABA Bank'),
(56, 'Madina', 'madina@ptt.com.kh', '$2y$10$d2y8vFG4tgncAJgU0R5GVeCf7Lna8CGdFi57/iXlGVt88UZuFuv9e', 0, 1, 1472, 'SD'),
(57, 'oilretail.pos', 'oilretail.pos@ptt.com.kh', '$2y$10$oT73TiQjAGy0Swsh3Gs.T.O.889tKl/JPfX7UpALG/FduSlKoQGTu', 0, 1, 1461, 'Wing Bank'),
(59, 'chantha.m', 'chantha.m@ptt.com.kh', '$2y$10$jdK3S07WoMsM5LDybaRgM.H/g0NKA4ZpxFniajqib2pebO7aukFy2', 0, 1, 1472, 'Wing Bank'),
(60, 'user', 'user@ptt.com', '$2y$10$WGTBKQADQowzPrvPFBlrauyj.ijhRq0/QWh7mkyfwDR4LDxX.jk3.', 0, 1, 1472, 'PTTCL');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users_rules`
--

CREATE TABLE `tbl_users_rules` (
  `rules_id` int(100) NOT NULL,
  `rules_name` varchar(255) DEFAULT NULL,
  `add_user_status` tinyint(1) DEFAULT NULL,
  `edit_user_status` tinyint(1) DEFAULT NULL,
  `delete_user_status` tinyint(1) DEFAULT NULL,
  `list_user_status` tinyint(1) DEFAULT NULL,
  `add_ticket_status` tinyint(1) DEFAULT NULL,
  `edit_ticket_status` tinyint(1) DEFAULT NULL,
  `delete_ticket_status` tinyint(1) DEFAULT NULL,
  `list_ticket_status` tinyint(1) DEFAULT NULL,
  `list_ticket_assign` tinyint(1) DEFAULT NULL,
  `add_user_rules` tinyint(1) DEFAULT NULL,
  `edit_user_rules` tinyint(1) DEFAULT NULL,
  `delete_user_rules` tinyint(1) DEFAULT NULL,
  `list_user_rules` tinyint(1) DEFAULT NULL,
  `add_station` tinyint(1) DEFAULT NULL,
  `edit_station` tinyint(1) DEFAULT NULL,
  `delete_station` tinyint(1) DEFAULT NULL,
  `list_station` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users_rules`
--

INSERT INTO `tbl_users_rules` (`rules_id`, `rules_name`, `add_user_status`, `edit_user_status`, `delete_user_status`, `list_user_status`, `add_ticket_status`, `edit_ticket_status`, `delete_ticket_status`, `list_ticket_status`, `list_ticket_assign`, `add_user_rules`, `edit_user_rules`, `delete_user_rules`, `list_user_rules`, `add_station`, `edit_station`, `delete_station`, `list_station`) VALUES
(1461, 'Admin', 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1),
(1472, 'user', 0, 0, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_station`
--
ALTER TABLE `tbl_station`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `station_id` (`station_id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `tbl_ticket_ibfk_1` (`user_create_ticket`);

--
-- Indexes for table `tbl_ticket_images`
--
ALTER TABLE `tbl_ticket_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl_ticket_images_ibfk_1` (`ticket_id`);

--
-- Indexes for table `tbl_ticket_track`
--
ALTER TABLE `tbl_ticket_track`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `modified_by` (`modified_by`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`users_id`),
  ADD KEY `rules_id` (`rules_id`);

--
-- Indexes for table `tbl_users_rules`
--
ALTER TABLE `tbl_users_rules`
  ADD PRIMARY KEY (`rules_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_station`
--
ALTER TABLE `tbl_station`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=440;

--
-- AUTO_INCREMENT for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=555;

--
-- AUTO_INCREMENT for table `tbl_ticket_images`
--
ALTER TABLE `tbl_ticket_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=548;

--
-- AUTO_INCREMENT for table `tbl_ticket_track`
--
ALTER TABLE `tbl_ticket_track`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `tbl_users_rules`
--
ALTER TABLE `tbl_users_rules`
  MODIFY `rules_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1489;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  ADD CONSTRAINT `tbl_ticket_ibfk_1` FOREIGN KEY (`user_create_ticket`) REFERENCES `tbl_users` (`users_id`),
  ADD CONSTRAINT `tbl_ticket_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `tbl_station` (`station_id`);

--
-- Constraints for table `tbl_ticket_images`
--
ALTER TABLE `tbl_ticket_images`
  ADD CONSTRAINT `tbl_ticket_images_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tbl_ticket` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_ticket_track`
--
ALTER TABLE `tbl_ticket_track`
  ADD CONSTRAINT `tbl_ticket_track_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tbl_ticket` (`ticket_id`),
  ADD CONSTRAINT `tbl_ticket_track_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `tbl_users` (`users_id`);

--
-- Constraints for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD CONSTRAINT `tbl_users_ibfk_1` FOREIGN KEY (`rules_id`) REFERENCES `tbl_users_rules` (`rules_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
